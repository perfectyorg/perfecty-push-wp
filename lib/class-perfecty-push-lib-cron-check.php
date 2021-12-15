<?php

use Perfecty_Push_Lib_Log as Log;

/***
 * Check cron functionality
 */
class Perfecty_Push_Lib_Cron_Check {

	public const HOOK             = 'perfecty_push_cron_check';
	private const FAILURES_COUNT  = 'perfecty_push_cron_failures';
	private const SCHEDULE_OFFSET = 60; // 60 seconds
	private const MAX_DIFF        = 60; // 60 seconds
	private const THRESHOLD       = 10; // consider error after this number of failures

	/**
	 * Runs the ticker and checks if the executions are correctly done
	 */
	public static function run() {
		// if the ticker is not running, we schedule it
		$scheduled = wp_next_scheduled( self::HOOK );
		if ( $scheduled === false ) {
			self::schedule_next_tick();
			return;
		}

		// get the failures count and the diff between scheduled and the current time
		$failures_count = get_option( self::FAILURES_COUNT );
		$diff           = $scheduled - time();

		if ( $diff < 0 && abs( $diff ) > self::MAX_DIFF ) {
			// something is wrong: even though it was scheduled in the future,
			// it is still scheduled but in the past and the difference is longer than
			// MAX_DIFF seconds. This means the cron is not running properly in this cycle
			Log::warning( 'Cron check missed, diff: ' . $diff . 's, failed attempt: ' . ( $failures_count + 1 ) );

			// increment failures count and un-schedule the event so that it's checked again after some minutes
			update_option( self::FAILURES_COUNT, $failures_count + 1 );
			wp_unschedule_event( $scheduled, self::HOOK );
		}

		if ( $failures_count >= self::THRESHOLD ) {
			if ( ! self::is_wp_cron_enabled() ) {
				Log::warning( 'WP Cron is disabled' );
			}

			// we have failed more than the threshold, we consider the cron is not
			// correctly working and log the error
			Log::error( 'Multiple cron checks have failed. Please check your cron configuration in wp-config.php' );
		}
	}

	/**
	 * This is the function being called on every scheduled event. It
	 * resets the failures count.
	 */
	public static function tick() {
		// reset failures count
		update_option( self::FAILURES_COUNT, 0 );

		Log::info( 'Cron check ticked' );
	}

	/**
	 * Schedules the execution of just one tick
	 */
	private static function schedule_next_tick() {
		$next_scheduled_time = time() + self::SCHEDULE_OFFSET;
		wp_schedule_single_event( $next_scheduled_time, self::HOOK );

		Log::info(
			'Next ticker scheduled at: ' .
			get_date_from_gmt( gmdate( 'Y-m-d H:i:s', intval( $next_scheduled_time ) ) ),
			'Y-m-d H:i:s'
		);
	}

	/**
	 * Checks if wp-cron is enabled
	 *
	 * @return bool
	 */
	private static function is_wp_cron_enabled() {
		return ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON );
	}
}
