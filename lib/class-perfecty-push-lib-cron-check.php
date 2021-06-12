<?php

use Perfecty_Push_Lib_Log as Log;

/***
 * Check cron functionality
 */
class Perfecty_Push_Lib_Cron_Check {

	private const CRON_HOOK = 'perfecty_push_cron_check';

	private const SCHEDULE_OFFSET = 1800; // 30 minutes

	private const SUCCESS_RUN_LOG_MSG = 'Cron monitor runned';

	private const NEXT_SCHED_LOG_MSG = 'Next cron check scheduled at: ';

	private const NEVER_SCHED_LOG_MSG = 'Cron monitor was not scheduled';

	private const NOT_RUNNED_LOG_MSG = 'Cron monitor was scheduled but not executed';

	private const LIMIT_LOGS_FOR_CALC = 10;

	/**
	 * The cron running status.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      bool    $cron_working    The cron running status.
	 */
	private static $cron_working = false;

	/**
	 * Initialize the class.
	 *
	 * @since    1.2.0
	 */
	public function __construct() {
		$this->schedule_cron_job();
	}

	/**
	 * Schedules the cron job once (this is the callback of the cronjob too).
	 */
	public function schedule_cron_job() {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			$now                 = time();
			$next_scheduled_time = $now + self::SCHEDULE_OFFSET;
			wp_schedule_single_event( $next_scheduled_time, self::CRON_HOOK );
			Log::info(
				self::SUCCESS_RUN_LOG_MSG . ' - ' . self::NEXT_SCHED_LOG_MSG . \
				get_date_from_gmt( gmdate( 'Y-m-d H:i:s', intval( $next_scheduled_time ) ) ),
				'Y-m-d H:i:s'
			);
		}
	}

	/**
	 * Checks if next scheduled action is in the future and returns cron status
	 *
	 * @return bool
	 */
	public static function get_cron_status() {
		$now = time();
		/**
		 * When wp-cron is disabled and a system cron is running we don't want to annoy users
		 * with a false positive warning. In this scenario, a false positive (a warning saying
		 * that no cron is set) could arise when the system's cron is set to call wp-cron.php
		 * with a period (let's call it SYSCRON_PERIOD) longer than SCHEDULE_OFFSET.
		 * We cannot directly read system cron settings, but we can guess the real periodicity
		 * (given by the combination of the 2 periods) by reading our logs (when enabled).
		 *
		 * The REAL_PERIOD (we estimate) will be equal to the SYSCRON_PERIOD when this
		 * is bigger (longer) than SCHEDULE_OFFSET.
		 * If SYSCRON_PERIOD is smaller (shorter) than SCHEDULE_OFFSET it will be in range
		 * [SCHEDULE_OFFSET; 2x SCHEDULE_OFFSET[
		 *
		 * The REAL_PERIOD can be calculated using this formula:
		 *
		 * IF ( SCHEDULE_OFFSET Mod ( SYSCRON_PERIOD ) > 0 ) {
		 *      REAL_PERIOD = SCHEDULE_OFFSET + SYSCRON_PERIOD - ( SCHEDULE_OFFSET Mod ( SYSCRON_PERIOD ) )
		 * } ELSE {
		 *      REAL_PERIOD = SCHEDULE_OFFSET
		 * }
		 */
		if ( ( ! self::is_wp_cron_enabled() ) && self::are_logs_enabled() ) {
			$last_success_logs = self::get_success_logs();
			$sys_cron_offset   = self::get_estimated_system_cron_offset( $last_success_logs );
			if ( $sys_cron_offset ) {
				$last_success_log = self::get_last_success_log( $last_success_logs );
				$scheduled        = $sys_cron_offset + $last_success_log;
			} else {
				$scheduled = wp_next_scheduled( self::CRON_HOOK );
			}
		} else {
			$scheduled = wp_next_scheduled( self::CRON_HOOK );
		}
		if ( false === $scheduled ) {
			Log::debug( self::NEVER_SCHED_LOG_MSG );
			return false;
		}
		if ( intval( $scheduled ) < $now ) {
			// We have a false positive in small/no traffice server, if the page that triggers wp-cron is Perfecty Push Admin.
			Log::debug( self::NOT_RUNNED_LOG_MSG );
			return false;
		}
		self::$cron_working = true;
		return true;
	}

	/**
	 * Checks if logs functionality is enabled
	 *
	 * @return bool
	 */
	private static function are_logs_enabled() {
		$options          = get_option( 'perfecty_push', array() );
		$are_logs_enabled = isset( $options['logs_enabled'] ) ? $options['logs_enabled'] : '';
		if ( '' === $are_logs_enabled ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Checks if wp-cron is enabled
	 *
	 * @return bool
	 */
	private static function is_wp_cron_enabled() {
		return ! ( var_export( DISABLE_WP_CRON, true ) ); // phpcs:ignore
	}

	/**
	 * Returns an array of rows indexed from 0 of the last 10 (LIMIT_LOGS_FOR_CALC) SUCCESS_RUN_LOG_MSG
	 *
	 * @return array
	 */
	private static function get_success_logs() {
		global $wpdb;
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT UNIX_TIMESTAMP(created_at)' .
				' FROM ' . $wpdb->prefix . 'perfecty_push_logs' .
				' WHERE message LIKE %s' .
				' ORDER BY created_at desc ' .
				' LIMIT %d OFFSET %d',
				$wpdb->esc_like( self::SUCCESS_RUN_LOG_MSG ) . '%',
				self::LIMIT_LOGS_FOR_CALC,
				0
			),
			ARRAY_N
		);
		foreach ( $rows as $row ) {
			$results[] = $row[0];
		}
		return $results;
	}

	/**
	 * Returns a value that is the average (max between avg and median) difference beetween last success executions of the monitor
	 *
	 * @param array $success_logs An array of the last (max) 10 success check logged.
	 * @return int|false
	 */
	private static function get_estimated_system_cron_offset( $success_logs ) {

		$num_logs = count( $success_logs );

		if ( $num_logs < 2 ) {
			return false;
		} else {
			sort( $success_logs );

			$sum = 0;
			$med = 0;
			$avg = 0;

			$i = 1;
			while ( $i < $num_logs ) {
				$offset    = ( intval( $success_logs[ $i ] ) - intval( $success_logs[ $i - 1 ] ) );
				$offsets[] = $offset;
				$sum      += $offset;
				$i++;
			}
			$num_offsets = count( $offsets );

			// Median value.
			sort( $offsets );
			$middleval = floor( ( $num_offsets - 1 ) / 2 ); // find the middle value, or the lowest middle value.
			if ( $num_offsets % 2 ) { // odd number, middle is the median.
				$med = $offsets[ $middleval ];
			} else { // even number, calculate avg of 2 medians.
				$low  = $offsets[ $middleval ];
				$high = $offsets[ $middleval + 1 ];
				$med  = intval( ( $low + $high ) / 2 );
			}

			// Average value.
			$avg = round( $sum / $num_offsets );
		}
		$max_value = max( $med, $avg );
		return $max_value;
	}

	/**
	 * Returns a value that is the average (max avg and median) difference beetween last success execution times of the monitor
	 *
	 * @param array $success_logs An array of the last (max) 10 success check logged.
	 * @return int|false
	 */
	private static function get_last_success_log( $success_logs ) {
		rsort( $success_logs );
		return $success_logs[0];
	}
}
