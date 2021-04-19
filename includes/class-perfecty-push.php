<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Perfecty_Push_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PERFECTY_PUSH_VERSION' ) ) {
			$this->version = PERFECTY_PUSH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'perfecty-push';

		$this->define_constants();
		$this->load_dependencies();
		$this->set_locale();
		if ( Class_Perfecty_Push_Lib_Utils::is_enabled() ) {
			$this->load_push_server();
		}
		$this->define_global_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Define the constants that will be needed later
	 */
	public function define_constants() {
		$options              = get_option( 'perfecty_push', array() );
		$vapid_public_key     = isset( $options['vapid_public_key'] ) ? $options['vapid_public_key'] : '';
		$vapid_private_key    = isset( $options['vapid_private_key'] ) ? $options['vapid_private_key'] : '';
		$service_worker_scope = isset( $options['service_worker_scope'] ) && ! empty( $options['service_worker_scope'] ) ? $options['service_worker_scope'] : '/perfecty/push';

		if ( ! defined( 'PERFECTY_PUSH_JS_DIR' ) ) {
			$path = plugin_dir_url( __DIR__ ) . 'public/js';
			define( 'PERFECTY_PUSH_JS_DIR', $path );
		}
		if ( ! defined( 'PERFECTY_PUSH_SERVICE_WORKER_SCOPE' ) ) {
			define( 'PERFECTY_PUSH_SERVICE_WORKER_SCOPE', $service_worker_scope );
		}
		if ( ! defined( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY' ) && $vapid_public_key ) {
			define( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY', $vapid_public_key );
		}
		if ( ! defined( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY' ) && $vapid_private_key ) {
			define( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY', $vapid_private_key );
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Perfecty_Push_Loader. Orchestrates the hooks of the plugin.
	 * - Perfecty_Push_i18n. Defines internationalization functionality.
	 * - Perfecty_Push_Admin. Defines all hooks for the admin area.
	 * - Perfecty_Push_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-perfecty-push-loader.php';

		/**
		 * The class responsible for the global hooks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-perfecty-push-global.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-perfecty-push-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-perfecty-push-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-perfecty-push-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-perfecty-push-users.php';

		/**
		 * Contains the form tables
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-perfecty-push-admin-notifications-table.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-perfecty-push-admin-users-table.php';

		/**
		 * Contains the lib/ definitions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/class-perfecty-push-lib-db.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/class-perfecty-push-lib-push-server.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/class-perfecty-push-lib-payload.php';

		$this->loader = new Perfecty_Push_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Perfecty_Push_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Perfecty_Push_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Bootstrap the Push Server
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_push_server() {
		$auth             = array();
		$plugin_activated = get_option( 'perfecty_push_activated', 0 );

		if ( defined( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY' ) && defined( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY' )
		&& PERFECTY_PUSH_VAPID_PUBLIC_KEY && PERFECTY_PUSH_VAPID_PRIVATE_KEY ) {
			$auth = array(
				'VAPID' => array(
					'subject'    => site_url(),
					'publicKey'  => PERFECTY_PUSH_VAPID_PUBLIC_KEY,
					'privateKey' => PERFECTY_PUSH_VAPID_PRIVATE_KEY,
				),
			);
		} elseif ( $plugin_activated == 1 ) {
			error_log( 'VAPID Keys are missing' );
			Class_Perfecty_Push_Lib_Utils::show_message( sprintf( esc_html( 'The VAPID keys are missing in Perfecty Push. Help: %1$s Generate the VAPID Keys. %2$s', 'perfecty-push-notifications' ), "<a href='https://github.com/rwngallego/perfecty-push-wp/wiki/Troubleshooting#the-vapid-keys-are-missing-in-perfecty-push-generate-the-vapid-keys' target='_blank'>", '</a>' ), 'warning' );
			Class_Perfecty_Push_Lib_Utils::disable();
			return false;
		}
		$vapid_generator = array( 'Minishlink\WebPush\VAPID', 'createVapidKeys' );

		Perfecty_Push_Lib_Push_Server::bootstrap( $auth, $vapid_generator );
		return true;
	}

	/**
	 * Register the global hooks that are not related to admin or public
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_global_hooks() {
		$global = new Perfecty_Push_Global();
		$this->loader->add_action( 'plugins_loaded', $global, 'upgrade_check' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Perfecty_Push_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_options' );
		$this->loader->add_action( 'perfecty_push_broadcast_notification_event', $plugin_admin, 'execute_broadcast_batch', 10, 1 );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'register_metaboxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'on_save_post' );
		$this->loader->add_action( 'transition_post_status', $plugin_admin, 'on_transition_post_status', 10, 3 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_notice' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Perfecty_Push_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'print_head' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'register_rest_endpoints' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Perfecty_Push_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
