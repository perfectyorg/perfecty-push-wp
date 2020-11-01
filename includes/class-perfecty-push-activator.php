<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		Perfecty_Push_Lib_Db::db_create();
		Perfecty_Push_Activator::default_options();
	}

	private static function default_options () {
		// Verifies if we already have both the vapid_public_key and vapid_private_key
		// If we don't, we generate them and save it
		$options = get_option('perfecty_push', []);
		if (!$options || (empty($options['vapid_public_key']) && empty($options['vapid_private_key']))){
			$vapidKeys = Perfecty_Push_Lib_Push_Server::create_vapid_keys();
			$new_options = [
				'vapid_public_key' => $vapidKeys['publicKey'],
				'vapid_private_key' => $vapidKeys['privateKey']
			];

			if (!update_option('perfecty_push', $new_options)) {
				error_log('Could not set the VAPID keys');
			}
		}
	}

}
