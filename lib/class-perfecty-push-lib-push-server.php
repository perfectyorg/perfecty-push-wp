<?php

use Perfecty_Push_Lib_Log as Log;

/***
 * Perfecty Push server.
 *
 * Important: Before using any method you need to bootstrap the lib.
 */
class Perfecty_Push_Lib_Push_Server {

	public const DEFAULT_BATCH_SIZE             = 1500;
	public const DEFAULT_PARALLEL_FLUSHING_SIZE = 50;
	public const BROADCAST_HOOK                 = 'perfecty_push_broadcast_notification_event';

	private static $auth;
	private static $webpush;
	private static $vapid_generator;
	private static $max_time;

	/**
	 * Bootstraps the Push Server.
	 *
	 * @param $auth array Vapid Keys
	 * @param $vapid_generator Callable Method that generates the vapid keys
	 * @param $webpush object Web push server
	 * @param $max_time int Max execution time in seconds, default: php_ini.max_execution_time
	 */
	public static function bootstrap( $auth, $vapid_generator, $webpush = null, $max_time = null ) {
		if ( ! is_callable( $vapid_generator ) ) {
			Log::error( "$vapid_generator must be a callable function" );
		}

		self::$auth            = $auth;
		self::$vapid_generator = $vapid_generator;
		self::$webpush         = $webpush;
		self::$max_time        = $max_time == null ? (int) ini_get( 'max_execution_time' ) : $max_time;
	}

	/**
	 * Get the Push Server instance
	 *
	 * @return object Web push server
	 * @throws Throwable Bubbles the internal exceptions.
	 */
	public static function get_push_server() {
		if ( ! self::$auth ) {
			Log::error( 'The VAPID auth keys were not found' );
			return null;
		}

		$options                = get_option( 'perfecty_push' );
		$parallel_flushing_size = isset( $options['parallel_flushing_size'] ) ? (int) esc_attr( $options['parallel_flushing_size'] ) : self::DEFAULT_PARALLEL_FLUSHING_SIZE;

		set_error_handler(
			function ( $errno, $errstr, $errfile, $errline ) {
				if ( strpos( $errstr, 'gmp extension is not loaded' ) !== false ) {
					// we know this, however we capture the E_WARNING because we have previously
					// informed the user about this in a nicer way.
					return true;
				}

				Log::error( 'Could not get the Push Server: ' . $errno . ' ' . $errstr . ' ' . $errfile . ' ' . $errline );
				return false; // we raise it to the next handler otherwise
			}
		);
		try {
			$webpush = Perfecty_Push_External_Webpush::get( self::$auth, array( 'batchSize' => $parallel_flushing_size ) );
			$webpush->setReuseVAPIDHeaders( true );
		} catch ( Throwable $ex ) {
			Log::error( 'Could not start the Push Server: ' . $ex->getMessage() . ', ' . $ex->getTraceAsString() );
			Perfecty_Push_Lib_Utils::show_message( esc_html( 'Could not start the Push Server, check the PHP error logs for more information.', 'perfecty-push-notifications' ), 'warning' );
			Perfecty_Push_Lib_Utils::disable();
			throw $ex;
		}
		restore_error_handler();

		return $webpush;
	}

	/**
	 * Creates the VAPI keys
	 *
	 * @return array an array with the VAPID keys
	 */
	public static function create_vapid_keys() {
		$vapid_keys = call_user_func( self::$vapid_generator );
		Log::info( 'VAPID Keys were created' );

		return $vapid_keys;
	}

	/**
	 * Schedules an async notification to all the users
	 *
	 * @param $payload array Payload to be sent, array
	 * @param $scheduled_time int|string a unix timestamp, or a string representing a date
	 * @return int | bool $notification_id if success, false otherwise
	 * @throws Exception
	 */
	public static function schedule_broadcast_async( $payload, $scheduled_time = null ) {
		Log::info( 'Scheduling a broadcast notification' );
		Log::debug( print_r( $payload, true ) );

		// required because is_plugin_active is needed when saving a post
		// and 'admin_init' hasn't been fired yet
		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		if ( ! is_array( $payload ) ) {
			$error = 'Payload should be an array';
			Log::error( $error );
			throw new Exception( $error );
		}
		$payload = apply_filters( 'perfecty_push_custom_payload', $payload );
		$payload = json_encode( $payload );

		if ( Perfecty_Push_Lib_Utils::is_disabled() ) {
			Log::error( 'Perfecty Push is disabled, fix the issues already reported.' );
			return false;
		}

		$options              = get_option( 'perfecty_push' );
		$use_action_scheduler = isset( $options['use_action_scheduler'] ) ? esc_attr( $options['use_action_scheduler'] ) : false;
		$batch_size           = isset( $options['batch_size'] ) ? (int) esc_attr( $options['batch_size'] ) : self::DEFAULT_BATCH_SIZE;

		if ( is_plugin_active( 'action-scheduler' ) || $use_action_scheduler ) {
			// Execute using action scheduler: https://actionscheduler.org/usage/
			Log::error( 'Action scheduler not implemented' );
			return false;
		} else {
			// Fallback to wp-cron
			if ( is_null( $scheduled_time ) ) {
				$scheduled_time = time();
			}
			if ( is_string( $scheduled_time ) ) {
				$date           = new DateTime( $scheduled_time );
				$scheduled_time = $date->getTimestamp();
			}
			$total_users     = Perfecty_Push_Lib_Db::get_total_users();
			$notification_id = Perfecty_Push_Lib_Db::create_notification( $payload, Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, $total_users, $batch_size, $scheduled_time );
			if ( ! $notification_id ) {
				Log::error( 'Could not schedule the notification.' );
				return false;
			}
			self::schedule_job( $notification_id, $scheduled_time );
			do_action( 'perfecty_push_broadcast_scheduled', $payload );

			return $notification_id;
		}
	}

	/**
	 * Schedule a notification to be executed by the cron job
	 *
	 * @param $notification_id
	 * @param $scheduled_time
	 *
	 * @since v1.4.1
	 */
	public static function schedule_job( $notification_id, $scheduled_time ) {
		$result = wp_schedule_single_event( $scheduled_time, self::BROADCAST_HOOK, array( $notification_id ) );
		Log::info( 'Scheduling job id=' . $notification_id . ', result: ' . $result );
		return $result;
	}

	/**
	 * Un-schedule a notification from the cron job
	 *
	 * @param $notification_id
	 * @param $scheduled_time
	 *
	 * @since v1.4.1
	 */
	public static function unschedule_job( $notification_id, $scheduled_time ) {
		$result = wp_unschedule_event( $scheduled_time, self::BROADCAST_HOOK, array( $notification_id ) );
		Log::info( 'Un-scheduling job id=' . $notification_id . ', result: ' . $result );
		return $result;
	}

	/**
	 * Send the notification to a WordPress user
	 *
	 * @param $wp_user_id int WordPress User Id
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @return array Array with [succeeded, failed]
	 * @throws Exception
	 */
	public static function notify( $wp_user_id, $payload ) {
		if ( ! is_array( $payload ) ) {
			$error = 'Payload should be an array';
			Log::error( $error );
			throw new Exception( $error );
		}
		$payload = apply_filters( 'perfecty_push_custom_payload', $payload );
		$payload = json_encode( $payload );

		$users = Perfecty_Push_Lib_Db::get_users_by_wp_user_id( $wp_user_id );

		$result = self::send_notification( $payload, $users );

		do_action( 'perfecty_push_wp_user_notified', $payload, $wp_user_id );

		return $result;
	}

	/**
	 * Schedule a broadcast notification job for all the users
	 *
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @return int $notification_id if success, false otherwise
	 * @throws Exception
	 */
	public static function broadcast( $payload ) {
		return self::schedule_broadcast_async( $payload );
	}

	/**
	 * Get the job notifications that are stalled and
	 * schedule the execution automatically
	 */
	public static function unleash_stalled() {
		$running = Perfecty_Push_Lib_Db::get_notifications_stalled();
		foreach ( $running as $item ) {
			if ( ! wp_next_scheduled( self::BROADCAST_HOOK, array( $item->id ) ) ) {
				self::schedule_job( $item->id, time() );
				Log::info( 'An stalled notification job was unleashed, id = ' . $item->id );
			}
		}
	}

	/**
	 * Returns true if the time has exceeded 80% of the maximum execution time
	 *
	 * @param $start_time float Start time in seconds (float)
	 *
	 * @return bool
	 * @since 1.4.0
	 */
	public static function time_limit_exceeded( $start_time ) {
		$elapsed_time = microtime( true ) - $start_time;
		if ( self::$max_time != 0 && ( $elapsed_time * 100 / self::$max_time ) > 80 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Execute one broadcast batch
	 *
	 * @param int $notification_id Notification id
	 *
	 * @return bool Succeeded or failed
	 */
	public static function execute_broadcast_batch( $notification_id ) {
		Log::info( 'Executing batch for job id=' . $notification_id );

		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		if ( ! $notification ) {
			Log::error( "Notification job $notification_id was not found" );
			return false;
		}

		// if it has been taken but not released, that means a wrong state
		if ( $notification->is_taken ) {
			Log::error( 'Halted, notification job already taken, notification_id: ' . $notification_id );
			return false;
		}

		// we check if it's a valid status
		// we only process running or scheduled jobs
		if ( $notification->status !== Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED &&
		$notification->status !== Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING ) {
			Log::error( 'Halted, received a job with an invalid status (' . $notification->status . '), notification_id: ' . $notification_id );
			return false;
		}

		// this is the first time we get here so we mark it as running
		if ( $notification->status == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED ) {
			Log::info( 'Marking job id=' . $notification_id . ' as running' );
			Perfecty_Push_Lib_Db::mark_notification_running( $notification_id );
		}

		Perfecty_Push_Lib_Db::take_notification( $notification_id );

		// we get the next batch, starting from $last_cursor we take $batch_size elements
		// we only fetch the active users (only_active)
		$total_succeeded = 0;
		$total_failed    = 0;
		$cursor          = $notification->last_cursor;
		$start_time      = microtime( true );
		while ( true ) {
			$users  = Perfecty_Push_Lib_Db::get_users( $cursor, $notification->batch_size );
			$count  = count( $users );
			$cursor = $cursor + $count;

			if ( $count == 0 ) {
				Log::info( 'Job id=' . $notification_id . ' completed, released' );
				$result = Perfecty_Push_Lib_Db::mark_notification_completed_untake( $notification_id );
				if ( ! $result ) {
					Log::error( "Could not mark the notification job $notification_id as completed" );
					break;
				}
				break;
			}

			$result    = self::send_notification( $notification->payload, $users );
			$succeeded = $result[0];
			$failed    = $result[1];
			if ( $succeeded !== 0 ) {
				Log::info( "Completed batch, successful: $succeeded, failed: $failed, cursor: $cursor" );
				$total_succeeded += $succeeded;
				$total_failed    += $failed;
			} else {
				Log::error( 'Error executing one batch for id=' . $notification_id );
				Perfecty_Push_Lib_Db::mark_notification_failed( $notification_id );
				Perfecty_Push_Lib_Db::untake_notification( $notification_id );
				break;
			}

			// check that we don't exceed 80% of max_execution_time
			// in case we do, we split the execution to a next cron cycle to avoid the termination of the script
			// if max_execution_time=0, we never split
			if ( self::time_limit_exceeded( $start_time ) ) {
				Log::warning( 'Time execution is reaching 80% of max_execution_time, moving to next cycle' );
				break;
			}
		}

		if ( $total_succeeded != 0 ) {
			$notification                    = Perfecty_Push_Lib_Db::get_notification( $notification_id );
			$notification->last_cursor       = $cursor;
			$notification->succeeded        += $total_succeeded;
			$notification->failed           += $total_failed;
			$notification->is_taken          = 0;
			$notification->last_execution_at = current_time( 'mysql', 1 );
			$result                          = Perfecty_Push_Lib_Db::update_notification( $notification );

			Log::info( 'Notification cycle for id=' . $notification_id . ' sent. Cursor: ' . $notification->last_cursor . ', Succeeded: ' . $total_succeeded . ', Failed: ' . $total_failed );
			if ( ! $result ) {
				Log::error( 'Could not update the notification after sending one batch' );
				return false;
			}

			if ( $notification->status === Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING ) {
				// execute the next batch
				if ( ! wp_next_scheduled( self::BROADCAST_HOOK, array( $notification_id ) ) ) {
					$result = wp_schedule_single_event( time(), self::BROADCAST_HOOK, array( $notification_id ) );
					Log::info( 'Scheduling next batch for id=' . $notification_id . ' . Result: ' . $result );
				} else {
					Log::warning( "Don't schedule next batch, it's already scheduled, id=" . $notification_id );
				}
			}
		}

		return true;
	}

	/**
	 * Send the notification to a set of users
	 *
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @param $users array List of users
	 * @return array Total (succeeded, failed) notifications sent
	 * @throws ErrorException
	 */
	public static function send_notification( $payload, $users ) {
		Log::info( 'Sending notification to ' . count( $users ) . ' users' );
		Log::debug( print_r( $payload, true ) );

		if ( ! self::$webpush ) {
			self::$webpush = self::get_push_server();
		}

		foreach ( $users as $item ) {
			Log::debug( 'Enqueuing notification to user id=' . $item->id );
			$push_user = Perfecty_Push_External_Webpush::subscription(
				$item->endpoint,
				$item->key_p256dh,
				$item->key_auth
			);

			self::$webpush->queueNotification( $push_user, $payload );
		}

		$succeeded = 0;
		$failed    = 0;
		foreach ( self::$webpush->flush() as $report ) {
			if ( $report->isSuccess() ) {
				Log::debug( 'Notification sent successfully' );
				$succeeded++;
			} else {
				Log::error( 'Failed to send one notification, error: ' . $report->getReason() );
				$failed++;

				$endpoint = $report->getEndpoint();
				if ( $report->isSubscriptionExpired() ) {
					Log::error( "User subscription has expired, removing it: $endpoint" );
					Perfecty_Push_Lib_Db::delete_user_by_endpoint( $endpoint );
					continue;
				}
				$response = $report->getResponse();
				if ( $response != null && $response->getStatusCode() == 403 ) {
					Log::error( "The endpoint should not be tried again, removing it: $endpoint" );
					Perfecty_Push_Lib_Db::delete_user_by_endpoint( $endpoint );
					continue;
				}
			}
		}
		return array( $succeeded, $failed );
	}
}
