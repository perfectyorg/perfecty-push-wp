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
	 * Performs a check of the DB version to run DB upgrades.
	 * This is particularly helpful when the plugin is updated
	 * because the register_activation_hook is not called.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function db_upgrade_check() {
		if ( get_option( 'perfecty_push_db_version' ) != PERFECTY_PUSH_DB_VERSION ) {
			Perfecty_Push_Lib_Db::db_create();
		}
	}
}
