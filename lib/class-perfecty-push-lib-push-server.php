<?php

use Minishlink\WebPush\Subscription;

/***
 * Perfecty Push server.
 *
 * Important: Before using any method you need to bootstrap the lib.
 */
class Perfecty_Push_Lib_Push_Server {

	private const DEFAULT_BATCH_SIZE = 30;
	private static $webpush;
	private static $vapid_generator;

	/**
	 * Bootstraps the Push Server.
	 *
	 * @param $webpush object Web push server
	 * @param $vapid_generator Callable Method that generates the vapid keys
	 */
	public static function bootstrap( $webpush, $vapid_generator ) {
		if ( ! is_callable( $vapid_generator ) ) {
			error_log( "$vapid_generator must be a callable function" );
		}

		self::$webpush         = $webpush;
		self::$vapid_generator = $vapid_generator;
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
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @return int $notification_id if success, false otherwise
	 */
	public static function schedule_broadcast_async( $payload ) {
		if ( ! is_string( $payload ) ) {
			$payload = json_encode( $payload );
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
			$total_subscriptions = Perfecty_Push_Lib_Db::total_subscriptions();
			$notification_id     = Perfecty_Push_Lib_Db::create_notification( $payload, Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, $total_subscriptions, $batch_size );
			if ( ! $notification_id ) {
				error_log( 'Could not schedule the notification.' );
				return false;
			}
			wp_schedule_single_event( time(), 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
			return $notification_id;
		}
	}

	/**
	 * Execute one broadcast batch
	 *
	 * @param $notification_id Notification id
	 *
	 * @return bool Succeeded or failed
	 */
	public static function execute_broadcast_batch( $notification_id ) {
		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		if ( ! $notification ) {
			error_log( "Notification $notification_id was not found" );
			return false;
		}

		// if it has been taken and was not released, that means a wrong state
		if ( $notification->is_taken ) {
			error_log( 'Halted, notification taken but not released, notification_id: ' . $notification_id );
			Perfecty_Push_Lib_Db::mark_notification_failed( $notification_id );
			return false;
		}

		Perfecty_Push_Lib_Db::take_notification( $notification_id );

		// we get the next batch, starting from $last_cursor we take $batch_size elements
		$subscriptions = Perfecty_Push_Lib_Db::get_subscriptions( $notification->last_cursor, $notification->batch_size );

		if ( count( $subscriptions ) == 0 ) {
			$result = Perfecty_Push_Lib_Db::mark_notification_completed_untake( $notification_id );
			if ( ! $result ) {
				error_log( "Could not mark the notification $notification_id as completed" );
				return false;
			}
			return true;
		}

		// we send one batch
		$result = self::send_notification( $notification->payload, $subscriptions );
		if ( is_array( $result ) ) {
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
			return false;
		}

		// execute the next batch
		wp_schedule_single_event( time(), 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
		return true;
	}

	/**
	 * Send the notification to a set of subscribers
	 *
	 * @param $payload array|string Payload to be sent, json encoded or array
	 * @param $subscriptions array List of subscriptions
	 * @return [$total, $succeeded] | string Total/succeeded messages or Error message
	 */
	public static function send_notification( $payload, $subscriptions ) {
		if ( ! is_string( $payload ) ) {
			$payload = json_encode( $payload );
		}

		foreach ( $subscriptions as $item ) {
			$push_subscription = new Subscription(
				$item->endpoint,
				$item->key_p256dh,
				$item->key_auth
			);

			self::$webpush->sendNotification( $push_subscription, $payload );
		}

		$total     = count( $subscriptions );
		$succeeded = 0;
		foreach ( self::$webpush->flush() as $result ) {
			if ( $result->isSuccess() ) {
				$succeeded++;
			} else {
				error_log( 'Failed to send one notification, error: ' . $result->getReason() );
			}
		}
		return array( $total, $succeeded );
	}
}
