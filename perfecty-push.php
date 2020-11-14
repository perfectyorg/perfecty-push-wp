<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/rwngallego
 * @since             1.0.0
 * @package           Perfecty_Push
 *
 * @wordpress-plugin
 * Plugin Name:       Perfecty Push Notifications
 * Plugin URI:        https://perfecty.co/push-server/wp/
 * Description:       Sef-hosted, Open Source and powerful <strong>Web Push Notifications</strong> plugin to send push notifications <strong>from your own server for free!</strong>
 * Version:           1.0.0
 * Author:            Rowinson Gallego
 * Author URI:        https://github.com/rwngallego
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       perfecty-push
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PERFECTY_PUSH_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-perfecty-push-activator.php
 */
function activate_perfecty_push() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-perfecty-push-activator.php';
	Perfecty_Push_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-perfecty-push-deactivator.php
 */
function deactivate_perfecty_push() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-perfecty-push-deactivator.php';
	Perfecty_Push_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_perfecty_push' );
register_deactivation_hook( __FILE__, 'deactivate_perfecty_push' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-perfecty-push.php';

/**
 * We load the composer dependencies. We only load the web-push-php library
 * if the gmp extension is enabled. In theory, composer libs can be used
 * in Wordpress plugins: https://github.com/awesomemotive/WP-Mail-SMTP
 */
if (extension_loaded('gmp')) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_perfecty_push() {

	$plugin = new Perfecty_Push();
	$plugin->run();

}
run_perfecty_push();
