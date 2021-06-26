<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Perfecty_Push_Lib_Log as Log;

/***
 * Perfecty Push server.
 *
 * Important: Before using any method you need to bootstrap the lib.
 */
class Perfecty_Push_Lib_Push_Server {

	public const DEFAULT_BATCH_SIZE = 30;

	private static $auth;
	private static $webpush;
	private static $vapid_generator;

	/**
	 * Bootstraps the Push Server.
	 *
	 * @param $auth array Vapid Keys
	 * @param $vapid_generator Callable Method that generates the vapid keys
	 * @param $webpush object Web push server
	 */
	public static function bootstrap( $auth, $vapid_generator, $webpush = null ) {
		if ( ! is_callable( $vapid_generator ) ) {
			Log::error( "$vapid_generator must be a callable function" );
		}

		self::$auth            = $auth;
		self::$vapid_generator = $vapid_generator;
		self::$webpush         = $webpush;
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
			$webpush = new WebPush( self::$auth );
			$webpush->setReuseVAPIDHeaders( true );
		} catch ( Throwable $ex ) {
			Log::error( 'Could not start the Push Server: ' . $ex->getMessage() . ', ' . $ex->getTraceAsString() );
			Class_Perfecty_Push_Lib_Utils::show_message( esc_html( 'Could not start the Push Server, check the PHP error logs for more information.', 'perfecty-push-notifications' ), 'warning' );
			Class_Perfecty_Push_Lib_Utils::disable();
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

		if ( Class_Perfecty_Push_Lib_Utils::is_disabled() ) {
			Log::error( 'Perfecty Push is disabled, fix the issues already reported.' );
			return false;
		}

		$options              = get_option( 'perfecty_push' );
		$use_action_scheduler = isset( $options['use_action_scheduler'] ) ? esc_attr( $options['use_action_scheduler'] ) : false;
		$batch_size           = isset( $options['batch_size'] ) ? esc_attr( $options['batch_size'] ) : self::DEFAULT_BATCH_SIZE;

		if ( is_plugin_active( 'action-scheduler' ) || $use_action_scheduler ) {
			// Execute using action scheduler: https://actionscheduler.org/usage/
			Log::error( 'Action scheduler not implemented' );
			return false;
		} else {
			// Fallback to wp-cron
			$total_users     = Perfecty_Push_Lib_Db::get_total_users();
			$notification_id = Perfecty_Push_Lib_Db::create_notification( $payload, Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, $total_users, $batch_size );
			if ( ! $notification_id ) {
				Log::error( 'Could not schedule the notification.' );
				return false;
			}
			if ( is_null( $scheduled_time ) ) {
				$scheduled_time = time();
			}
			if ( is_string( $scheduled_time ) ) {
				$date           = new DateTime( $scheduled_time );
				$scheduled_time = $date->getTimestamp();
			}
			$result = wp_schedule_single_event( $scheduled_time, 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
			Log::info( 'Scheduling job id=' . $notification_id . ', result: ' . $result );

			do_action( 'perfecty_push_broadcast_scheduled', $payload );

			return $notification_id;
		}
	}

	/**
	 * Send the notification to a WordPress user
	 *
	 * @param $wp_user_id int WordPress User Id
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @return array Array with [total, succeeded]
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
			Log::error( 'Halted, notification taken but not released, notification_id: ' . $notification_id );
			Perfecty_Push_Lib_Db::mark_notification_failed( $notification_id );
			Perfecty_Push_Lib_Db::untake_notification( $notification_id );
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
		$users = Perfecty_Push_Lib_Db::get_users( $notification->last_cursor, $notification->batch_size, 'created_at', 'desc' );

		if ( count( $users ) == 0 ) {
			Log::info( 'Job id=' . $notification_id . ' completed, released' );
			$result = Perfecty_Push_Lib_Db::mark_notification_completed_untake( $notification_id );
			if ( ! $result ) {
				Log::error( "Could not mark the notification job $notification_id as completed" );
				return false;
			}
			return true;
		}

		// we send one batch
		$result = self::send_notification( $notification->payload, $users );
		if ( is_array( $result ) ) {
			$notification               = Perfecty_Push_Lib_Db::get_notification( $notification_id );
			$total_batch                = $result[0];
			$succeeded                  = $result[1];
			$notification->last_cursor += $total_batch;
			$notification->succeeded   += $succeeded;
			$notification->is_taken     = 0;
			$result                     = Perfecty_Push_Lib_Db::update_notification( $notification );

			Log::info( 'Notification batch for id=' . $notification_id . ' sent. Cursor: ' . $notification->last_cursor . ', Total: ' . $total_batch . ', Succeeded: ' . $succeeded );
			if ( ! $result ) {
				Log::error( 'Could not update the notification after sending one batch' );
				return false;
			}
		} else {
			Log::error( 'Error executing one batch for id=' . $notification_id . ', result: ' . $result );
			Perfecty_Push_Lib_Db::mark_notification_failed( $notification_id );
			Perfecty_Push_Lib_Db::untake_notification( $notification_id );
			return false;
		}

		// execute the next batch
		$result = wp_schedule_single_event( time(), 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
		Log::info( 'Scheduling next batch for id=' . $notification_id . ' . Result: ' . $result );

		return true;
	}

	/**
	 * Send the notification to a set of users
	 *
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @param $users array List of users
	 * @return array Array with [total, succeeded]
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
			$push_user = new Subscription(
				$item->endpoint,
				$item->key_p256dh,
				$item->key_auth
			);

			self::$webpush->queueNotification( $push_user, $payload );
		}

		$total     = count( $users );
		$succeeded = 0;
		foreach ( self::$webpush->flush() as $report ) {
			if ( $report->isSuccess() ) {
				Log::debug( 'Notification sent successfully' );
				$succeeded++;
			} else {
				Log::error( 'Failed to send one notification, error: ' . $report->getReason() );

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
		return array( $total, $succeeded );
	}
}
