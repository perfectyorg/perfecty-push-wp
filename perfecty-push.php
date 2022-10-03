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
 * Plugin URI:        https://wordpress.org/plugins/perfecty-push-notifications
 * Description:       Self-hosted, Open Source and powerful <strong>Web Push Notifications</strong> plugin to send push notifications <strong>from your own server for free!</strong>
 * Version:           1.6.2
 * Author:            Perfecty
 * Author URI:        https://perfecty.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       perfecty-push-notifications
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
define( 'PERFECTY_PUSH_VERSION', '1.6.2' );

/**
 * DB Version of the plugin
 */
define( 'PERFECTY_PUSH_DB_VERSION', 6 );

/**
 * The basename of the plugin
 */
define( 'PERFECTY_PUSH_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during the plugin activation.
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
 * We load the dependencies and check for the gmp extension
 */
require plugin_dir_path( __FILE__ ) . 'lib/class-perfecty-push-lib-utils.php';
if ( version_compare( PHP_VERSION, '7.2.0', '>=' ) ) {
	Perfecty_Push_Lib_Utils::check_gmp();
} else {
	error_log( sprintf( 'Wrong PHP version: %s', PHP_VERSION ) );
	Perfecty_Push_Lib_Utils::show_message( esc_html( 'Perfecty Push requires PHP >= 7.2.0', 'perfecty-push-notifications' ), 'error' );
	Perfecty_Push_Lib_Utils::disable();
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
