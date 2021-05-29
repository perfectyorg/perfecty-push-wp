<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

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
			error_log( "$vapid_generator must be a callable function" );
		}

		self::$auth            = $auth;
		self::$vapid_generator = $vapid_generator;
		self::$webpush         = $webpush;
	}

	/**
	 * Get the Push Server instance
	 *
	 * @return object Web push server
	 */
	public static function getPushServer() {
		if ( ! self::$auth ) {
			error_log( 'The VAPID auth keys were not found' );
			return null;
		}

		set_error_handler(
			function ( $errno, $errstr, $errfile, $errline ) {
				if ( strpos( $errstr, 'gmp extension is not loaded' ) !== false ) {
					// we know this, however we capture the E_WARNING because we have previously
					// informed the user about this in a nicer way
					return true;
				}
				return false; // we raise it to the next handler otherwise
			}
		);
		try {
			$webpush = new WebPush( self::$auth );
			$webpush->setReuseVAPIDHeaders( true );
		} catch ( \Exception $ex ) {
			error_log( 'Could not start the Push Server: ' . $ex->getMessage() . ', ' . $ex->getTraceAsString() );
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
		// required because is_plugin_active is needed when saving a post
		// and 'admin_init' hasn't been fired yet
		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		if ( ! is_array( $payload ) ) {
			throw new Exception( 'Payload should be an array' );
		}
		$payload = json_encode( $payload );

		if ( Class_Perfecty_Push_Lib_Utils::is_disabled() ) {
			error_log( 'Perfecty Push is disabled, fix the issues already reported.' );
			return false;
		}

		$options              = get_option( 'perfecty_push' );
		$use_action_scheduler = isset( $options['use_action_scheduler'] ) ? esc_attr( $options['use_action_scheduler'] ) : false;
		$batch_size           = isset( $options['batch_size'] ) ? esc_attr( $options['batch_size'] ) : self::DEFAULT_BATCH_SIZE;

		if ( is_plugin_active( 'action-scheduler' ) || $use_action_scheduler ) {
			// Execute using action scheduler: https://actionscheduler.org/usage/
			error_log( 'Action scheduler not implemented' );
			return false;
		} else {
			// Fallback to wp-cron
			$total_users     = Perfecty_Push_Lib_Db::get_total_users( true );
			$notification_id = Perfecty_Push_Lib_Db::create_notification( $payload, Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, $total_users, $batch_size );
			if ( ! $notification_id ) {
				error_log( 'Could not schedule the notification.' );
				return false;
			}
			if ( is_null( $scheduled_time ) ) {
				$scheduled_time = time();
			}
			if ( is_string( $scheduled_time ) ) {
				$date           = new DateTime( $scheduled_time );
				$scheduled_time = $date->getTimestamp();
			}
			wp_schedule_single_event( $scheduled_time, 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
			return $notification_id;
		}
	}

    /**
     * Send the notification to a WordPress user
     *
     * @param $wp_user_id int WordPress User Id
     * @param $payload array|string Payload to be sent, json encoded or array
     * @return array Array with [total, succeeded]
     * @throws ErrorException
     */
    public static function notify ( $wp_user_id, $payload ) {
        $users = Perfecty_Push_Lib_Db::get_users_by_wp_user_id( $wp_user_id );

        return self::send_notification( $payload, $users );
    }

    /**
     * Schedule a broadcast notification job for all the users
     *
     * @param $payload array|string Payload to be sent, json encoded or array
     * @return int $notification_id if success, false otherwise
     * @throws Exception
     */
    public static function broadcast ( $payload ) {
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
		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		if ( ! $notification ) {
			error_log( "Notification $notification_id was not found" );
			return false;
		}

		// if it has been taken but not released, that means a wrong state
		if ( $notification->is_taken ) {
			error_log( 'Halted, notification taken but not released, notification_id: ' . $notification_id );
			Perfecty_Push_Lib_Db::mark_notification_failed( $notification_id );
			Perfecty_Push_Lib_Db::untake_notification( $notification_id );
			return false;
		}

		// we check if it's a valid status
		// we only process running or scheduled jobs
		if ( $notification->status !== Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED &&
		$notification->status !== Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING ) {
			error_log( 'Halted, received a job with an invalid status (' . $notification->status . '), notification_id: ' . $notification_id );
			return false;
		}

		// this is the first time we get here so we mark it as running
		if ( $notification->status == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED ) {
			Perfecty_Push_Lib_Db::mark_notification_running( $notification_id );
		}

		Perfecty_Push_Lib_Db::take_notification( $notification_id );

		// we get the next batch, starting from $last_cursor we take $batch_size elements
		// we only fetch the active users (only_active)
		$users = Perfecty_Push_Lib_Db::get_users( $notification->last_cursor, $notification->batch_size, 'created_at', 'desc', true );

		if ( count( $users ) == 0 ) {
			$result = Perfecty_Push_Lib_Db::mark_notification_completed_untake( $notification_id );
			if ( ! $result ) {
				error_log( "Could not mark the notification $notification_id as completed" );
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
			if ( ! $result ) {
				error_log( 'Could not update the notification after sending one batch' );
				return false;
			}
		} else {
			error_log( "Error executing one batch, result: $result, notification_id: " . $notification_id );
			Perfecty_Push_Lib_Db::mark_notification_failed( $notification_id );
			Perfecty_Push_Lib_Db::untake_notification( $notification_id );
			return false;
		}

		// execute the next batch
		wp_schedule_single_event( time(), 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
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
		if ( ! is_string( $payload ) ) {
			$payload = json_encode( $payload );
		}
		if ( ! self::$webpush ) {
			self::$webpush = self::getPushServer();
		}

		foreach ( $users as $item ) {
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
				$succeeded++;
			} else {
				error_log( 'Failed to send one notification, error: ' . $report->getReason() );

				$endpoint = $report->getEndpoint();
				if ( $report->isSubscriptionExpired() ) {
					error_log( "User subscription has expired, removing it: $endpoint" );
					Perfecty_Push_Lib_Db::delete_user_by_endpoint( $endpoint );
					continue;
				}
				$response = $report->getResponse();
				if ( $response != null && $response->getStatusCode() == 403 ) {
					error_log( "The endpoint should not be tried again, removing it: $endpoint" );
					Perfecty_Push_Lib_Db::delete_user_by_endpoint( $endpoint );
					continue;
				}
			}
		}
		return array( $total, $succeeded );
	}
}
