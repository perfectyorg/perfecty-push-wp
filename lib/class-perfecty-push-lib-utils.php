<?php

/**
 * Class Class_Perfecty_Push_Lib_Utils
 *
 * Contains utilities for checking the Push Server and show messages
 */
class Class_Perfecty_Push_Lib_Utils {


	/**
	 * Check if the Push Server is enabled
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return ! self::is_disabled();
	}

	/**
	 * Check if the Push Server is disabled
	 *
	 * @return bool
	 */
	public static function is_disabled() {
		return defined( 'PERFECTY_PUSH_DISABLED' ) && PERFECTY_PUSH_DISABLED == true;
	}

	/**
	 * Disable the Push Server
	 */
	public static function disable() {
		if ( ! defined( 'PERFECTY_PUSH_DISABLED' ) ) {
			define( 'PERFECTY_PUSH_DISABLED', true );
		}
	}

	/**
	 * Displays a message
	 *
	 * @param $message
     * @param $type Default: success, values: success, warning, error
	 */
	public static function show_message( $message, $type = 'success' ) {
		$notice = array(
			'type'    => $type,
			'message' => $message,
		);
		set_transient( 'perfecty_push_admin_notice', $notice );
	}

	/**
	 * Clean the transients used to show notice messages
	 */
	public static function clean_messages() {
		delete_transient( 'perfecty_push_admin_notice' );
	}

	/**
	 * Check the gmp extension
	 *
	 * If not no gmp, PHP=7.2 and missing VAPID keys, it will disable the Push Server
	 */
	public static function check_gmp() {
		$gmp_loaded     = extension_loaded( 'gmp' );
		$options        = get_option( 'perfecty_push', array() );
		$has_vapid_keys = ! empty( $options['vapid_public_key'] ) && ! empty( $options['vapid_private_key'] );
		if ( ! $has_vapid_keys && ! $gmp_loaded && version_compare( PHP_VERSION, '7.3', '<' ) ) {
			error_log( sprintf( 'Missing VAPID keys, and the gmp extension is not enabled on PHP < 7.3 (current: %s)', PHP_VERSION ) );
			self::show_message( "Perfecty Push cannot generate the VAPID keys automatically. Help: <a href='https://github.com/rwngallego/perfecty-push-wp/wiki/Troubleshooting#cannot-generate-the-vapid-keys-automatically' target='_blank'>Cannot generate the VAPID keys automatically.</a>", 'warning' );
			self::disable();
		} elseif ( $has_vapid_keys && ! $gmp_loaded && version_compare( PHP_VERSION, '7.3', '<' ) ) {
			self::show_message( "Perfecty Push performance is not optimal because of your current setup. Help: <a href='https://github.com/rwngallego/perfecty-push-wp/wiki/Troubleshooting#better-performance' target='_blank'>Better performance</a>", 'warning' );
		}
	}

    /**
     * Check that the tables have been created
     */
	public static function check_database() {
        global $wpdb;

        $user_table = Perfecty_Push_Lib_Db::users_table();
	    $notifications_table = Perfecty_Push_Lib_Db::notifications_table();

        if (!$wpdb->get_var("SHOW TABLES LIKE '$user_table'") ||
            !$wpdb->get_var("SHOW TABLES LIKE '$notifications_table'")) {
            self::show_message("The tables for Perfecty Push are missing, check the error logs and reactivate the plugin again.", 'error');
        }
    }
}
