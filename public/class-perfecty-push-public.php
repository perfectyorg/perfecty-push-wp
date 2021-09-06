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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/perfecty-push-sdk/dist/perfecty-push-sdk.min.js', array(), $this->version, true );
	}

	/**
	 * Prints the header content
	 *
	 * @since    1.0.0
	 */
	public function print_head() {
		$options = get_option( 'perfecty_push' );

		Perfecty_Push_Lib_Push_Server::unleash_stalled();
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-public-head.php';
	}

	/**
	 * Register the REST endpoints
	 *
	 * @since 1.0.0
	 */
	public function register_rest_endpoints() {
		$users = new Perfecty_Push_Users();

		// JS SDK friendly routes
		register_rest_route(
			'perfecty-push',
			'/v1/push/users',
			array(
				'methods'             => array( 'POST' ),
				'callback'            => array( $users, 'register' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'perfecty-push',
			'/v1/push/users/(?P<user_id>[a-zA-Z0-9-]+)/unregister',
			array(
				'methods'             => array( 'POST' ),
				'callback'            => array( $users, 'unregister' ),
				'permission_callback' => '__return_true',
				'args'                => array( 'user_id' => array() ),
			)
		);
		register_rest_route(
			'perfecty-push',
			'/v1/push/users/(?P<user_id>[a-zA-Z0-9-]+)',
			array(
				'methods'             => array( 'GET' ),
				'callback'            => array( $users, 'get_user' ),
				'permission_callback' => '__return_true',
				'args'                => array( 'user_id' => array() ),
			)
		);
	}
}
