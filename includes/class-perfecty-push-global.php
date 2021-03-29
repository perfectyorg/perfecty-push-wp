<?php

/**
 * The file that defines the global hooks and actions
 *
 * This class defines the different actions that are global and not
 * exclusive to the public or admin area
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 */

/**
 * The global loader class.
 *
 * This class defines the global actions to run
 * and that are not dependant on the admin or the public sections
 *
 * @since      1.0.0
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_Global {

	/**
	 * Performs a check of the DB and options upgrade
	 * This is particularly helpful when the plugin is updated
	 * because the register_activation_hook is not called on upgrades.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function upgrade_check() {
		$plugin_activated = get_option( 'perfecty_push_activated', 0 );

		if ( $plugin_activated == 1 ) {
			Class_Perfecty_Push_Lib_Utils::check_database();
		}

		if ( get_option( 'perfecty_push_version' ) != PERFECTY_PUSH_VERSION ) {
			// upgrade options
			if ( $plugin_activated == 1 ) {
				$options = get_option( 'perfecty_push', array() );
				if ( ! isset( $options['service_worker_scope'] ) ) {
					// this is before 1.0.7
					$options['service_worker_scope'] = '/';
					update_option( 'perfecty_push', $options );
				}
			}
			update_option( 'perfecty_push_version', PERFECTY_PUSH_VERSION );
		}
		if ( get_option( 'perfecty_push_db_version' ) != PERFECTY_PUSH_DB_VERSION ) {
			// upgrade db
			Perfecty_Push_Lib_Db::db_create();
		}
	}
}
