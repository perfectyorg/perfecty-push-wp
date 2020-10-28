<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/public
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$options = get_option('perfecty_push', []);
		$vapid_public_key = isset($options['vapid_public_key']) ? $options['vapid_public_key'] : '';
		$server_url = isset($options['server_url']) ? $options['server_url'] : '127.0.0.1:8777';

		if (!defined('PERFECTY_PUSH_JS_DIR')) {
			$path = plugin_dir_url(__FILE__) . "js";
			define('PERFECTY_PUSH_JS_DIR', $path);
		}
		if (!defined('PERFECTY_PUSH_SERVER_URL')) {
			define('PERFECTY_PUSH_SERVER_URL', $server_url);
		}
		if (!defined('PERFECTY_PUSH_VAPID_PUBLIC_KEY')) {
			define('PERFECTY_PUSH_VAPID_PUBLIC_KEY', $vapid_public_key);
		}

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/perfecty-push-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/perfecty-push-public.js', array('jquery'), $this->version, true );
	}

	/**
	 * Prints the header content
	 *
	 * @since    1.0.0
	 */
	public function print_head() {
		require_once plugin_dir_path( __FILE__) . 'partials/perfecty-push-public-head.php';
	}
}
