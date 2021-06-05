<?php
/**
 * The file that defines the cron check hook
 *
 * @link       https://github.com/perfectyorg
 * @since      1.2.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 */

/**
 * The Cron Check class.
 *
 * This class defines all code necessary to monitor that a cron system (wp_cron or system cron) is working.
 *
 * @since      1.2.0
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 * @author     Giuseppe Foti <foti.giuseppe@gmail.com>
 */
class Perfecty_Push_Cron_Check {

	private const CRON_HOOK = 'perfecty_push_cron_check';

	private const SCHEDULE_OFFSET = 1800; // 30 minutes

	private const LOG_ENABLED = true;

	private const LOGS_TO_STORE = 5;

	private const LOGS_OPTION_NAME = 'perfecty_push_cron_check_logs';

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
			$now = time();
			wp_schedule_single_event( $now + self::SCHEDULE_OFFSET, self::CRON_HOOK );
			if ( self::LOG_ENABLED ) {
				self::log_execution( $now );
			}
		}
	}

	/**
	 * Checks if next scheduled action is in the future and returns cron status
	 *
	 * @return bool
	 */
	public static function get_cron_status() {
		$now       = time();
		$scheduled = wp_next_scheduled( self::CRON_HOOK );
		if ( false === $scheduled ) {
			return false;
		}
		if ( intval( $scheduled ) < $now ) {
			return false;
		}
		self::$cron_working = true;
		return true;
	}

	/**
	 * Returns a css class for cron monitor box in admin dashboard
	 *
	 * @return string
	 */
	public static function get_cron_check_box_class() {
		if ( self::get_cron_status() ) {
			return 'perfecty-push-cron-working';
		} else {
			return 'perfecty-push-cron-stopped';
		}
	}

	/**
	 * Returns cron monitor box text
	 *
	 * @return string
	 */
	public static function get_cron_check_box_text() {
		$checks = self::get_last_logs();
		$text   = '';
		$msgs   = array();

		if ( ! empty( $checks ) ) {
			foreach ( $checks as $timestamp ) {
				$msgs[] = esc_html__( 'Cron monitor runned at: ', 'perfecty-push-notifications' ) . get_date_from_gmt( gmdate( 'Y-m-d H:i:s', intval( $timestamp ) ), 'Y-m-d H:i:s' );
			}
		} else {
			$msgs[] = esc_html__( 'Cron monitor never runned ', 'perfecty-push-notifications' );
		}
		$msgs[] = '&nbsp;';
		$msgs[] = esc_html__( 'Next check scheduled at: ', 'perfecty-push-notifications' ) . get_date_from_gmt( gmdate( 'Y-m-d H:i:s', intval( wp_next_scheduled( self::CRON_HOOK ) ) ), 'Y-m-d H:i:s' );
		$msgs[] = '&nbsp;';

		if ( false === self::$cron_working ) {
			$msgs[] = esc_html__( 'A cron system has to be enabled to let notifications work!', 'perfecty-push-notifications' );
		} else {
			$msgs[] = '<b>' . esc_html__( 'A cron system is running!', 'perfecty-push-notifications' ) . '</b>';
		}
		$msgs[] = '<b>' . esc_html__( 'DISABLE_WP_CRON is set to ', 'perfecty-push-notifications' ) . var_export( DISABLE_WP_CRON, true ) . '</b>'; // phpcs:ignore
		foreach ( $msgs as $msg ) {
			$text .= '<div class="perfecty-push-stats-text"><span>' . $msg . '</span></div>';
		}
		return $text;
	}

	/**
	 * Logs the last execution time;
	 *
	 * @param int $time The epoch time when last scheduled is executed.
	 */
	private function log_execution( $time ) {
		$log   = self::get_last_logs();
		$count = count( $log );
		if ( $count >= self::LOGS_TO_STORE ) {
			array_shift( $log );
		}
		$log[] = $time;
		update_option( self::LOGS_OPTION_NAME, $log );
	}

	/**
	 * Get last log array
	 *
	 * @return array $log Arrays ot timestamps of last executions recorded
	 */
	private static function get_last_logs() {
		return get_option( self::LOGS_OPTION_NAME );
	}
}
