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
			Perfecty_Push_Lib_Utils::check_database();
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
				if ( version_compare( get_option( 'perfecty_push_version' ), '1.1.0', '<' ) ) {
					// this is before 1.1.0
					// we reset the value because now we're using a different approach
					$options['server_url'] = '';
					update_option( 'perfecty_push', $options );
				}
				if ( version_compare( get_option( 'perfecty_push_version' ), '1.1.3', '<' ) ) {
					// this is before 1.1.3
					// we set it as enabled so we don't change the existing IP collection
					$options['segmentation_enabled'] = 1;
					update_option( 'perfecty_push', $options );
				}
				if ( version_compare( get_option( 'perfecty_push_version' ), '1.3.0', '<' ) && get_option( 'site_icon' ) ) {
					// this is before 1.3.0
					// we use the already defined icon.
					$options['notifications_default_icon'] = get_option( 'site_icon' );
					update_option( 'perfecty_push', $options );
				}
				if ( version_compare( get_option( 'perfecty_push_version' ), '1.4.0', '<' ) ) {
					// this is before 1.4.0
					// we set it as true by default
					$options['settings_send_welcome_message'] = 1;
					// this is a new parameter
					$options['parallel_flushing_size'] = Perfecty_Push_Lib_Push_Server::DEFAULT_PARALLEL_FLUSHING_SIZE;
					// we've removed the suffix in perfecty-push-public-head.php
					if ( isset( $options['server_url'] ) && ! empty( $options['server_url'] ) ) {
						$options['server_url'] = $options['server_url'] . 'perfecty-push';
					}
					// we've changed the meaning of batch_size
					$options['batch_size'] = Perfecty_Push_Lib_Push_Server::DEFAULT_BATCH_SIZE;
					update_option( 'perfecty_push', $options );
				}
				if ( version_compare( get_option( 'perfecty_push_version' ), '1.6.0', '<' ) ) {
					// this is before 1.6.0
					// we use the existing preferences
					if ( isset( $options['logs_enabled'] ) && $options['logs_enabled'] == 1 ) {
						$options['log_driver'] = 'db';
						$options['log_level']  = 'debug';
					} else {
						$options['log_driver'] = 'errorlog';
						$options['log_level']  = 'error';
					}
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
