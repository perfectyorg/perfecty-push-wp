<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Perfecty_Push_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Perfecty_Push_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/perfecty-push-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery.timepicker', plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.min.css', array(), '1.3.5', 'screen' );
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui/themes/smoothness/jquery-ui.min.css', array(), '1.12.1', 'screen' );
	}


	/**
	 * Print the WordPress directory plugin links
	 *
	 * @param array $actions Array with links.
	 *
	 * @return array
	 * @since    1.3.2
	 */
	public function plugin_directory_links( $actions ) {
		$links   = array(
			'<a href="' . admin_url( 'admin.php?page=perfecty-push-options' ) . '">' . esc_html__( 'Settings', 'perfecty-push-notifications' ) . '</a>',
			'<a href="https://docs.perfecty.org/" target="_blank">' . esc_html__( 'Documentation', 'perfecty-push-notifications' ) . '</a>',
		);
		$actions = array_merge( $actions, $links );
		return $actions;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook_suffix The page this is being called
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Perfecty_Push_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Perfecty_Push_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		if ( $hook_suffix === 'toplevel_page_perfecty-push' ) {
			// only load it in the Dashboard page
			// ChartJs has known conflict issues: https://github.com/chartjs/Chart.js/issues/3168.
			wp_enqueue_script( 'chartjs', plugin_dir_url( __FILE__ ) . 'js/chart.bundle.min.js', array( 'jquery' ), '2.9.4', false );
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/perfecty-push-admin.js', array( 'jquery', 'wp-i18n' ), $this->version, false );
		wp_enqueue_script( 'jquery-timepicker', plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.min.js', array( 'jquery' ), '1.3.5', false );
		wp_enqueue_script( 'html5-fallback', plugin_dir_url( __FILE__ ) . 'js/html5-fallback.js', array( 'jquery-ui-datepicker', 'jquery-timepicker' ), $this->version, false );
	}

	 /**
	  * Register the plugin page in the admin menu
	  *
	  * @since 1.0.0
	  */
	public function register_admin_menu() {
		add_menu_page(
			'Perfecty Push',
			'Perfecty Push',
			'manage_options',
			'perfecty-push',
			array( $this, 'print_dashboard_page' ),
			'dashicons-smiley'
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'Dashboard', 'perfecty-push-notifications' ),
			esc_html__( 'Dashboard', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push',
			array( $this, 'print_dashboard_page' )
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'Send notification', 'perfecty-push-notifications' ),
			esc_html__( 'Send notification', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push-send-notification',
			array( $this, 'print_send_notification_page' )
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'Notification jobs', 'perfecty-push-notifications' ),
			esc_html__( 'Notification jobs', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push-notifications',
			array( $this, 'print_notifications_page' )
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'Users', 'perfecty-push-notifications' ),
			esc_html__( 'Users', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push-users',
			array( $this, 'print_users_page' )
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'Settings', 'perfecty-push-notifications' ),
			esc_html__( 'Settings', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push-options',
			array( $this, 'print_options_page' )
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'Logs', 'perfecty-push-notifications' ),
			esc_html__( 'Logs', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push-logs',
			array( $this, 'print_logs_page' )
		);

		add_submenu_page(
			'perfecty-push',
			esc_html__( 'About', 'perfecty-push-notifications' ),
			esc_html__( 'About', 'perfecty-push-notifications' ),
			'manage_options',
			'perfecty-push-about',
			array( $this, 'print_about_page' )
		);
	}

	/**
	 * Register the plugin options
	 *
	 * @since 1.0.0
	 */
	public function register_options() {
		register_setting(
			'perfecty_group',
			'perfecty_push',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'perfecty_push_widget_settings',
			esc_html__( 'Public widget', 'perfecty-push-notifications' ),
			array( $this, 'print_dialog_section' ),
			'perfecty-push-options'
		);

		add_settings_field(
			'widget_enabled',
			esc_html__( 'Enabled', 'perfecty-push-notifications' ),
			array( $this, 'print_widget_enabled' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'visits_to_display_prompt',
			esc_html__( 'Display after this number of visits', 'perfecty-push-notifications' ),
			array( $this, 'print_visits_to_display_prompt' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'widget_ask_permissions_directly',
			esc_html__( 'Do not use widgets (ask permissions directly)', 'perfecty-push-notifications' ),
			array( $this, 'print_widget_ask_permissions_directly' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'widget_hide_bell_after_subscribe',
			esc_html__( 'Hide bell after subscribing', 'perfecty-push-notifications' ),
			array( $this, 'print_widget_hide_bell_after_subscribe' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'dialog_title',
			esc_html__( 'Subscribe text', 'perfecty-push-notifications' ),
			array( $this, 'print_dialog_title' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'dialog_submit',
			esc_html__( 'Continue text', 'perfecty-push-notifications' ),
			array( $this, 'print_dialog_submit' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'dialog_cancel',
			esc_html__( 'Cancel text', 'perfecty-push-notifications' ),
			array( $this, 'print_dialog_cancel' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'settings_title',
			esc_html__( 'Bell title', 'perfecty-push-notifications' ),
			array( $this, 'print_settings_title' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'settings_subscribed',
			esc_html__( 'Opt-in text', 'perfecty-push-notifications' ),
			array( $this, 'print_settings_opt_in' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'settings_update_error',
			esc_html__( 'Message on update error', 'perfecty-push-notifications' ),
			array( $this, 'print_settings_update_error' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'settings_send_welcome_message',
			esc_html__( 'Send a confirmation notification after subscribe', 'perfecty-push-notifications' ),
			array( $this, 'print_settings_send_welcome_message' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_field(
			'settings_welcome_message',
			esc_html__( 'Welcome notification text', 'perfecty-push-notifications' ),
			array( $this, 'print_settings_welcome_message' ),
			'perfecty-push-options',
			'perfecty_push_widget_settings'
		);

		add_settings_section(
			'perfecty_push_javascript_sdk_settings',
			esc_html__( 'Javascript SDK', 'perfecty-push-notifications' ),
			array( $this, 'print_javascript_sdk_section' ),
			'perfecty-push-options'
		);

		add_settings_field(
			'service_worker_scope',
			esc_html__( 'Service Worker Scope', 'perfecty-push-notifications' ),
			array( $this, 'print_service_worker_scope' ),
			'perfecty-push-options',
			'perfecty_push_javascript_sdk_settings'
		);

		add_settings_field(
			'unregister_conflicts',
			esc_html__( 'Remove conflicting workers (Push Services only)', 'perfecty-push-notifications' ),
			array( $this, 'print_unregister_conflicts' ),
			'perfecty-push-options',
			'perfecty_push_javascript_sdk_settings'
		);

		add_settings_field(
			'unregister_conflicts_expression',
			esc_html__( 'Custom conflict detection', 'perfecty-push-notifications' ),
			array( $this, 'print_unregister_conflicts_expression' ),
			'perfecty-push-options',
			'perfecty_push_javascript_sdk_settings'
		);

		add_settings_field(
			'widget_debugging_enabled',
			esc_html__( 'Enable client logs', 'perfecty-push-notifications' ),
			array( $this, 'print_widget_debugging_enabled' ),
			'perfecty-push-options',
			'perfecty_push_javascript_sdk_settings'
		);

		add_settings_section(
			'perfecty_push_notifications_settings',
			esc_html__( 'Notifications', 'perfecty-push-notifications' ),
			array( $this, 'print_notifications_section' ),
			'perfecty-push-options'
		);

		add_settings_field(
			'notifications_interaction_required',
			esc_html__( 'Fixed notifications (do not auto hide)', 'perfecty-push-notifications' ),
			array( $this, 'print_notifications_interaction_required' ),
			'perfecty-push-options',
			'perfecty_push_notifications_settings'
		);

		add_settings_field(
			'notifications_default_icon',
			esc_html__( 'Default Icon', 'perfecty-push-notifications' ),
			array( $this, 'print_notifications_default_icon' ),
			'perfecty-push-options',
			'perfecty_push_notifications_settings'
		);

		add_settings_section(
			'perfecty_push_segmentation_settings',
			esc_html__( 'Segmentation and Tracking', 'perfecty-push-notifications' ),
			array( $this, 'print_segmentation_section' ),
			'perfecty-push-options'
		);

		add_settings_field(
			'segmentation_enabled',
			esc_html__( 'Enable and collect data from users', 'perfecty-push-notifications' ),
			array( $this, 'print_segmentation_enabled' ),
			'perfecty-push-options',
			'perfecty_push_segmentation_settings'
		);

		add_settings_field(
			'segmentation_tracking_utm',
			esc_html__( 'UTM analytics', 'perfecty-push-notifications' ),
			array( $this, 'print_segmentation_tracking_utm' ),
			'perfecty-push-options',
			'perfecty_push_segmentation_settings'
		);

		add_settings_section(
			'perfecty_push_metabox_settings',
			esc_html__( 'Post publishing', 'perfecty-push-notifications' ),
			array( $this, 'print_metabox_section' ),
			'perfecty-push-options'
		);

		add_settings_field(
			'check_send_on_publish',
			esc_html__( 'Always send a notification', 'perfecty-push-notifications' ),
			array( $this, 'print_default_send_on_publish' ),
			'perfecty-push-options',
			'perfecty_push_metabox_settings'
		);

		add_settings_section(
			'perfecty_push_self_hosted_settings',
			esc_html__( 'Self-hosted server', 'perfecty-push-notifications' ),
			array( $this, 'print_self_hosted_section' ),
			'perfecty-push-options'
		);

		add_settings_field(
			'vapid_private_key',
			esc_html__( 'Vapid private key', 'perfecty-push-notifications' ),
			array( $this, 'print_vapid_private_key' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);
		add_settings_field(
			'vapid_public_key',
			esc_html__( 'Vapid public key', 'perfecty-push-notifications' ),
			array( $this, 'print_vapid_public_key' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);

		add_settings_field(
			'server_url',
			esc_html__( 'Custom REST Url', 'perfecty-push-notifications' ),
			array( $this, 'print_server_url' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);

		add_settings_field(
			'batch_size',
			esc_html__( 'Batch size', 'perfecty-push-notifications' ),
			array( $this, 'print_batch_size' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);

		add_settings_field(
			'parallel_flushing_size',
			esc_html__( 'Parallel flushing size', 'perfecty-push-notifications' ),
			array( $this, 'print_parallel_flushing_size' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);

		add_settings_field(
			'log_driver',
			esc_html__( 'Log driver', 'perfecty-push-notifications' ),
			array( $this, 'print_log_driver' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);

		add_settings_field(
			'log_level',
			esc_html__( 'Log level', 'perfecty-push-notifications' ),
			array( $this, 'print_log_level' ),
			'perfecty-push-options',
			'perfecty_push_self_hosted_settings'
		);
	}

	/**
	 * Executes the cron check. Note that this will only run
	 * when the admin area is loaded (uses the `admin_init` hook)
	 */
	public function check_cron() {
		// This cron check is only intended to be run from the admin area, or
		// when the `admin_init` hook is called
		Perfecty_Push_Lib_Cron_Check::run();
	}

	/**
	 * Execute the next broadcast batch
	 * This is a WordPress action called from wp-cron
	 *
	 * @param $notification_id int Notification ID
	 */
	public function execute_broadcast_batch( $notification_id ) {
		Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
	}

	/**
	 * Register the metaboxes
	 */
	public function register_metaboxes() {
		$post_types   = get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			)
		);
		$post_types[] = 'post';

		add_meta_box( 'perfecty_push_post_metabox', 'Perfecty Push', array( $this, 'display_post_metabox' ), $post_types, 'side', 'high' );
	}

	/**
	 * Displays the metabox in the post
	 *
	 * @param $post object Contains the post
	 */
	public function display_post_metabox( $post ) {
		wp_nonce_field( 'perfecty_push_post_metabox', 'perfecty_push_post_metabox_nonce' );
		$options               = get_option( 'perfecty_push', array() );
		$check_send_on_publish = isset( $options['check_send_on_publish'] ) && $options['check_send_on_publish'] == 1;

		$send_notification = ! empty( get_post_meta( $post->ID, '_perfecty_push_send_on_publish', true ) );

		$notification_title = get_post_meta( $post->ID, '_perfecty_push_notification_custom_title', true );
		$notification_body  = get_post_meta( $post->ID, '_perfecty_push_notification_custom_body', true );

		$is_customized = ! empty( trim( $notification_body ) ) || ! empty( trim( $notification_title ) );

		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-post-metabox.php';
	}

	/**
	 * Actions triggered when saving posts
	 * Hook triggered when saving the posts
	 *
	 * @param $post_id
	 */
	public function on_save_post( $post_id ) {
		// check the nonce
		if ( ! isset( $_POST['perfecty_push_post_metabox_nonce'] ) ) {
			return $post_id;
		}
		$nonce = $_POST['perfecty_push_post_metabox_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'perfecty_push_post_metabox' ) ) {
			return $post_id;
		}

		// nothing on auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check capabilities
		if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

		$send_notification = ! empty( $_POST['perfecty_push_send_on_publish'] );
		update_post_meta( $post_id, '_perfecty_push_send_on_publish', $send_notification );

		$notification_title = $_POST['perfecty_push_notification_custom_title'] ?? '';
		update_post_meta( $post_id, '_perfecty_push_notification_custom_title', esc_html( $notification_title ) );

		$notification_body = $_POST['perfecty_push_notification_custom_body'] ?? '';
		update_post_meta( $post_id, '_perfecty_push_notification_custom_body', esc_html( $notification_body ) );
	}

	/**
	 * Action triggered when a post changes status
	 *
	 * @param $new_status string New status
	 * @param $old_status string Old status
	 * @param $post WP_Post Post
	 */
	public function on_transition_post_status( $new_status, $old_status, $post ) {
		// related: https://github.com/WordPress/gutenberg/issues/15094
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		$send_notification = false;
		$is_customized     = false;
		if ( isset( $_POST['perfecty_push_post_metabox_nonce'] ) &&
			wp_verify_nonce( $_POST['perfecty_push_post_metabox_nonce'], 'perfecty_push_post_metabox' ) ) {
			// we do this because on_transition_post_status is triggered before on_save_post by WordPress
			$send_notification  = ! empty( $_POST['perfecty_push_send_on_publish'] );
			$notification_title = $_POST['perfecty_push_notification_custom_title'] ?? '';
			$notification_body  = $_POST['perfecty_push_notification_custom_body'] ?? '';
		} else {
			$send_notification  = ! empty( get_post_meta( $post->ID, '_perfecty_push_send_on_publish', true ) );
			$notification_title = get_post_meta( $post->ID, '_perfecty_push_notification_custom_title', true );
			$notification_body  = get_post_meta( $post->ID, '_perfecty_push_notification_custom_body', true );
		}

		if ( 'publish' == $new_status && $send_notification ) {
			$body               = trim( $notification_body ) ? $notification_body : html_entity_decode( get_the_title( $post ) );
			$url_to_open        = get_the_permalink( $post );
			$notification_title = trim( $notification_title ) ? $notification_title : '';

			// we use this to check if the post has a thumbnail because has_post_thumbnail could return true even if no post thumbnail is set.
			$featured_image_url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
			if ( ! empty( $featured_image_url ) ) {
				$post_thumbnail = get_the_post_thumbnail_url( $post->ID );
			} else {
				$post_thumbnail = $this->get_first_image_url( $post );
			}

			$payload = Perfecty_Push_Lib_Payload::build( $body, $notification_title, $post_thumbnail, $url_to_open );
			$result  = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );

			if ( $result === false ) {
				error_log( esc_html__( 'Could not schedule the broadcast async, check the logs', 'perfecty-push-notifications' ) );
				Perfecty_Push_Lib_Utils::show_message( esc_html__( 'Could not send the notification', 'perfecty-push-notifications' ), 'error' );
			} else {
				if ( isset( $_POST['perfecty_push_post_metabox_nonce'] ) ) {
					// once we sent the notification, we reset the checkbox when the
					// hook was triggered after clicking the save button.
					unset( $_POST['perfecty_push_send_on_publish'] );
				}
				update_post_meta( $post->ID, '_perfecty_push_send_on_publish', false );

				Perfecty_Push_Lib_Utils::show_message( '<strong>Perfecty Push</strong> ' . esc_html__( 'has sent a notification for the recently published post:', 'perfecty-push-notifications' ) . ' ' . $body );
			}
		}
	}

	/**
	 * Show the admin notices
	 *
	 * Gets the transient 'perfecty_push_admin_notice' which is an array with the type as key and a message as value
	 */
	public function show_admin_notice() {
		$notice = get_transient( 'perfecty_push_admin_notice' );

		if ( $notice && is_array( $notice ) ) {
			$type    = $notice['type'];
			$message = $notice['message'];

			if ( $type === 'warning' ) {
				printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
			} elseif ( $type === 'error' ) {
				printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', $message );
			} else {
				printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $message );
			}

			delete_transient( 'perfecty_push_admin_notice' );
		}
	}

	/**
	 * Renders the dashboard page
	 *
	 * @since 1.0.0
	 */
	public function print_dashboard_page() {
		$end_date   = new DateTime();
		$start_date = new DateTime();
		$start_date->sub( new DateInterval( 'P7D' ) );

		$users_stats         = Perfecty_Push_Lib_Db::get_users_stats();
		$notifications_stats = Perfecty_Push_Lib_Db::get_notifications_stats();
		$jobs_stats          = Perfecty_Push_Lib_Db::get_jobs_stats();
		$daily_stats         = Perfecty_Push_Lib_Db::get_notifications_daily_stats( $start_date, $end_date );

		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-dashboard.php';
	}

	/**
	 * Renders the options page
	 *
	 * @since 1.0.0
	 */
	public function print_options_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-options.php';
	}

	/**
	 * Renders the notifications page
	 *
	 * @since 1.0.0
	 */
	public function print_notifications_page() {
		$page = esc_html( sanitize_key( $_REQUEST['page'] ) );

		if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) &&
			isset( $_GET['action'] ) && $_GET['action'] == 'view' ) {
			$id   = intval( $_REQUEST['id'] );
			$item = Perfecty_Push_Lib_Db::get_notification( $id );

			$item->payload = json_decode( $item->payload );

			require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-notifications-view.php';
			return;
		}

		$table    = new Perfecty_Push_Admin_Notifications_Table();
		$affected = $table->prepare_items();
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-notifications.php';
	}

	/**
	 * Renders the users page
	 *
	 * @since 1.0.0
	 */
	public function print_users_page() {
		$page = esc_html( sanitize_key( $_REQUEST['page'] ) );

		if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) &&
			isset( $_GET['action'] ) && $_GET['action'] == 'view' ) {
			$id                   = intval( $_REQUEST['id'] );
			$item                 = Perfecty_Push_Lib_Db::get_user( $id );
			$options              = get_option( 'perfecty_push', array() );
			$segmentation_enabled = isset( $options['segmentation_enabled'] ) && $options['segmentation_enabled'] == 1;

			require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-users-view.php';
			return true;
		}

		$table    = new Perfecty_Push_Admin_Users_Table();
		$affected = $table->prepare_items();
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-users.php';
	}

	/**
	 * Renders the send notification page
	 *
	 * @since 1.0.0
	 */
	public function print_send_notification_page() {
		$message = '';
		$notice  = '';

		$default = array(
			'perfecty-push-send-notification-title'       => '',
			'perfecty-push-send-notification-message'     => '',
			'perfecty-push-send-notification-url-to-open' => '',
			'perfecty-push-send-notification-image'       => '',
			'perfecty-push-send-notification-timeoffset'  => '',
		);

		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'perfecty_push_send_notification' ) ) {
			$item = shortcode_atts( $default, $_REQUEST );

			$validation_result = $this->validate_notification_message( $item );
			if ( $validation_result === true ) {
				// filter
				$item['perfecty-push-send-notification-title']       = sanitize_text_field( $item['perfecty-push-send-notification-title'] );
				$item['perfecty-push-send-notification-message']     = sanitize_textarea_field( $item['perfecty-push-send-notification-message'] );
				$item['perfecty-push-send-notification-url-to-open'] = sanitize_text_field( $item['perfecty-push-send-notification-url-to-open'] );
				$item['perfecty-push-send-notification-image']       = sanitize_text_field( $item['perfecty-push-send-notification-image'] );
				$item['perfecty-push-send-notification-timeoffset']  = sanitize_text_field( $item['perfecty-push-send-notification-timeoffset'] );

				$payload        = Perfecty_Push_Lib_Payload::build( $item['perfecty-push-send-notification-message'], $item['perfecty-push-send-notification-title'], $item['perfecty-push-send-notification-image'], $item['perfecty-push-send-notification-url-to-open'] );
				$timeoffset     = intval( $item['perfecty-push-send-notification-timeoffset'] );
				$scheduled_time = self::calculate_scheduled_time_from_offset( $timeoffset );

				// send notification
				$result = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload, $scheduled_time );

				if ( $result === false ) {
					  $notice = esc_html__( 'Could not schedule the notification, check the logs', 'perfecty-push-notifications' );
				} else {
					$message = esc_html__( 'The notification job has been scheduled', 'perfecty-push-notifications' );
					if ( $item['perfecty-push-send-notification-timeoffset'] ) {
						$message .= ' ' . esc_html__( 'at', 'perfecty-push-notifications' ) . ' ';
						$message .= get_date_from_gmt( date( 'Y-m-d H:i:s', $scheduled_time ), 'Y-m-d H:i:s' );
					}

					// we clear the form
					$item = $default;
				}
			} else {
				$notice = $validation_result;
			}
		} else {
			$item = $default;
		}

		add_meta_box(
			'perfecty_push_send_notification_meta_box',
			esc_html__( 'Notification details', 'perfecty-push-notifications' ),
			array( $this, 'print_send_notification_metabox' ),
			'perfecty-push-send-notification',
			'normal'
		);

		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-send-notification.php';
	}

	/**
	 * Calculate scheduled time
	 *
	 * @param array $input Contains the settings
	 * @return int The notification scheduled time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	 */
	private function calculate_scheduled_time_from_offset( $timeoffset ) {
		$timeoffset = intval( $timeoffset );
		return time() + $timeoffset;
	}

	/**
	 * Validates the notification details
	 *
	 * @param array $item Contains the entry
	 */
	public function validate_notification_message( $item ) {
		$messages = array();

		if ( empty( $item['perfecty-push-send-notification-title'] ) ) {
			$messages[] = esc_html__( 'The title is required', 'perfecty-push-notifications' );
		}

		if ( empty( $item['perfecty-push-send-notification-message'] ) ) {
			$messages[] = esc_html__( 'The message is required', 'perfecty-push-notifications' );
		}

		if ( empty( $messages ) ) {
			return true;
		} else {
			return implode( '<br />', $messages );
		}
	}

	/**
	 * Renders the send notification metabox
	 *
	 * @since 1.0.0
	 */
	public function print_send_notification_metabox( $item ) {
		$options  = get_option( 'perfecty_push' );
		$icon_id  = isset( $options['notifications_default_icon'] ) ? esc_attr( $options['notifications_default_icon'] ) : '';
		$icon_url = wp_get_attachment_url( $icon_id );
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-send-notification-metabox.php';
	}

	/**
	 * Renders the logs page
	 *
	 * @since 1.2.0
	 */
	public function print_logs_page() {
		$page = esc_html( sanitize_key( $_REQUEST['page'] ) );

		$table        = new Perfecty_Push_Admin_Logs_Table();
		$affected     = $table->prepare_items();
		$options      = get_option( 'perfecty_push', array() );
		$enabled_logs = isset( $options['log_driver'] ) && $options['log_driver'] == 'db';
		if ( ! $enabled_logs ) {
			$message = esc_html__( 'You are using a logger that does not support the Log viewer (supported: Database)', 'perfecty-push-notifications' );
		}
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-logs.php';
	}

	/**
	 * Renders the about page
	 *
	 * @since 1.0.0
	 */
	public function print_about_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-about.php';
	}

	/**
	 * Sanitize the settings
	 *
	 * @param array $input Contains the settings
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = array();
		$options   = get_option( 'perfecty_push' );

		// checkboxes
		if ( isset( $input['widget_enabled'] ) ) {
			$new_input['widget_enabled'] = 1;
		} else {
			$new_input['widget_enabled'] = 0;
		}
		if ( isset( $input['unregister_conflicts'] ) ) {
			$new_input['unregister_conflicts'] = 1;
		} else {
			$new_input['unregister_conflicts'] = 0;
		}
		if ( isset( $input['widget_debugging_enabled'] ) ) {
			$new_input['widget_debugging_enabled'] = 1;
		} else {
			$new_input['widget_debugging_enabled'] = 0;
		}
		if ( isset( $input['widget_hide_bell_after_subscribe'] ) ) {
			$new_input['widget_hide_bell_after_subscribe'] = 1;
		} else {
			$new_input['widget_hide_bell_after_subscribe'] = 0;
		}
		if ( isset( $input['widget_ask_permissions_directly'] ) ) {
			$new_input['widget_ask_permissions_directly'] = 1;
		} else {
			$new_input['widget_ask_permissions_directly'] = 0;
		}
		if ( isset( $input['segmentation_enabled'] ) ) {
			$new_input['segmentation_enabled'] = 1;
		} else {
			$new_input['segmentation_enabled'] = 0;
		}
		if ( isset( $input['notifications_interaction_required'] ) ) {
			$new_input['notifications_interaction_required'] = 1;
		} else {
			$new_input['notifications_interaction_required'] = 0;
		}
		if ( isset( $input['check_send_on_publish'] ) ) {
			$new_input['check_send_on_publish'] = 1;
		} else {
			$new_input['check_send_on_publish'] = 0;
		}
		if ( isset( $input['settings_send_welcome_message'] ) ) {
			$new_input['settings_send_welcome_message'] = 1;
		} else {
			$new_input['settings_send_welcome_message'] = 0;
		}

		// text
		if ( isset( $input['service_worker_scope'] ) ) {
			$new_input['service_worker_scope'] = sanitize_text_field( $input['service_worker_scope'] );
		}
		if ( isset( $input['dialog_title'] ) ) {
			$new_input['dialog_title'] = sanitize_text_field( $input['dialog_title'] );
		}
		if ( isset( $input['dialog_submit'] ) ) {
			$new_input['dialog_submit'] = sanitize_text_field( $input['dialog_submit'] );
		}
		if ( isset( $input['dialog_cancel'] ) ) {
			$new_input['dialog_cancel'] = sanitize_text_field( $input['dialog_cancel'] );
		}
		if ( isset( $input['settings_title'] ) ) {
			$new_input['settings_title'] = sanitize_text_field( $input['settings_title'] );
		}
		if ( isset( $input['settings_opt_in'] ) ) {
			$new_input['settings_opt_in'] = sanitize_text_field( $input['settings_opt_in'] );
		}
		if ( isset( $input['settings_update_error'] ) ) {
			$new_input['settings_update_error'] = sanitize_text_field( $input['settings_update_error'] );
		}
		if ( isset( $input['settings_welcome_message'] ) ) {
			$new_input['settings_welcome_message'] = sanitize_text_field( $input['settings_welcome_message'] );
		}
		if ( isset( $input['vapid_public_key'] ) ) {
			$new_input['vapid_public_key'] = sanitize_text_field( $input['vapid_public_key'] );
		}
		if ( isset( $input['vapid_private_key'] ) ) {
			$new_input['vapid_private_key'] = sanitize_text_field( $input['vapid_private_key'] );
		}
		if ( isset( $input['server_url'] ) ) {
			$new_input['server_url'] = sanitize_text_field( $input['server_url'] );
		}
		if ( isset( $input['batch_size'] ) ) {
			$new_input['batch_size'] = intval( sanitize_text_field( $input['batch_size'] ) );
		}
		if ( isset( $input['parallel_flushing_size'] ) ) {
			$new_input['parallel_flushing_size'] = intval( sanitize_text_field( $input['parallel_flushing_size'] ) );
		}
		if ( isset( $input['notifications_default_icon'] ) ) {
			$new_input['notifications_default_icon'] = intval( sanitize_text_field( $input['notifications_default_icon'] ) );
		}
		if ( isset( $input['unregister_conflicts_expression'] ) ) {
			$new_input['unregister_conflicts_expression'] = sanitize_text_field( $input['unregister_conflicts_expression'] );
		}
		if ( isset( $input['segmentation_tracking_utm'] ) ) {
			$new_input['segmentation_tracking_utm'] = sanitize_text_field( $input['segmentation_tracking_utm'] );
		}
		if ( isset( $input['visits_to_display_prompt'] ) ) {
			$new_input['visits_to_display_prompt'] = intval( sanitize_text_field( $input['visits_to_display_prompt'] ) );
		}
		if ( isset( $input['log_driver'] ) ) {
			$new_input['log_driver'] = sanitize_text_field( $input['log_driver'] );
		}
		if ( isset( $input['log_level'] ) ) {
			$new_input['log_level'] = sanitize_text_field( $input['log_level'] );
		}

		if ( empty( $options['vapid_public_key'] ) && empty( $options['vapid_private_key'] ) &&
			! empty( $new_input['vapid_public_key'] ) && ! empty( $new_input['vapid_private_key'] ) ) {
			Perfecty_Push_Lib_Utils::clean_messages();
		}
		return $new_input;
	}

	/**
	 * Print the general section info
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_section() {
		print esc_html__( 'The controls in your front page (bell control/opt-in box).', 'perfecty-push-notifications' );
	}

	/**
	 * Print the general section info
	 *
	 * @since 1.3.0
	 */
	public function print_javascript_sdk_section() {
		print esc_html__( 'Client SDK that handles the subscription/push notifications.', 'perfecty-push-notifications' );
	}

	/**
	 * Print the self hosted section info
	 *
	 * @since 1.0.0
	 */
	public function print_self_hosted_section() {
		print esc_html__( 'Configure your self-hosted Perfecty Push Server.', 'perfecty-push-notifications' );
	}

	/**
	 * Print the notifications section info
	 *
	 * @since 1.3.0
	 */
	public function print_notifications_section() {
		print esc_html__( 'Notifications that the users receive (Mobile/Desktop).', 'perfecty-push-notifications' );
	}

	/**
	 * Print the segmentation section info
	 *
	 * @since 1.1.3
	 */
	public function print_segmentation_section() {
		print esc_html__( 'Campaign targeting and behaviour tracking.', 'perfecty-push-notifications' );
	}

	/**
	 * Print the Tracking UTM parameters
	 *
	 * @since 1.3.0
	 */
	public function print_segmentation_tracking_utm() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['segmentation_tracking_utm'] ) ? esc_attr( $options['segmentation_tracking_utm'] ) : '';

		printf(
			'Example: <div class="perfecty-push-options-unregister-conflicts-regex">%s</div>' .
			'<input type="text" id="perfecty_push[segmentation_tracking_utm]"' .
			'name="perfecty_push[segmentation_tracking_utm]" value="%s"/>',
			esc_html( 'utm_source=perfecty-push&utm_medium=web-push&utm_campaign=my-campaign-name' ),
			esc_html( $value )
		);
	}


	/**
	 * Print the Vapid public key
	 *
	 * @since 1.0.0
	 */
	public function print_vapid_public_key() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['vapid_public_key'] ) ? esc_attr( $options['vapid_public_key'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[vapid_public_key]"' .
			'name="perfecty_push[vapid_public_key]" value="%s"/>',
			esc_html( $value )
		);
	}

	/**
	 * Print the Vapid private key
	 *
	 * @since 1.0.0
	 */
	public function print_vapid_private_key() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['vapid_private_key'] ) ? esc_attr( $options['vapid_private_key'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[vapid_private_key]"' .
			'name="perfecty_push[vapid_private_key]" value="%s"/>',
			esc_html( $value )
		);
	}

	/**
	 * Print the server_url option
	 *
	 * @since 1.0.0
	 */
	public function print_server_url() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['server_url'] ) ? esc_attr( $options['server_url'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[server_url]"' .
			'name="perfecty_push[server_url]" value="%s" placeholder="%s"/>',
			esc_html( $value ),
			get_rest_url( null, 'perfecty-push' )
		);
	}

	/**
	 * Print the batch_size option
	 *
	 * @since 1.0.0
	 */
	public function print_batch_size() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['batch_size'] ) ? esc_attr( $options['batch_size'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[batch_size]"' .
			'name="perfecty_push[batch_size]" value="%s"/>' .
			'<div>%s <a href="%s" target="_blank">%s</div>',
			esc_html( $value ),
			esc_html( 'High values require a longer script execution time. See: ' ),
			'https://docs.perfecty.org/wp/performance-improvements/#adjusting-the-batch_size-parameter',
			'Batch size'
		);
	}

	/**
	 * Print the parallel_flushing_size option
	 *
	 * @since 1.4.0
	 */
	public function print_parallel_flushing_size() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['parallel_flushing_size'] ) ? esc_attr( $options['parallel_flushing_size'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[parallel_flushing_size]"' .
			'name="perfecty_push[parallel_flushing_size]" value="%s"/>' .
			'<div>%s <a href="%s" target="_blank">%s</div>',
			esc_html( $value ),
			esc_html( 'Notifications to send in parallel. Please read: ' ),
			'https://docs.perfecty.org/wp/performance-improvements/#adjusting-the-parallel_flushing_size-parameter',
			'Parallel flushing'
		);
	}

	/**
	 * Print the widget_enabled option
	 *
	 * @since 1.0.0
	 */
	public function print_widget_enabled() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['widget_enabled'] ) ? esc_attr( $options['widget_enabled'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[widget_enabled]"' .
			'name="perfecty_push[widget_enabled]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the debugging public enabled option
	 *
	 * @since 1.0.0
	 */
	public function print_widget_debugging_enabled() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['widget_debugging_enabled'] ) ? esc_attr( $options['widget_debugging_enabled'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[widget_debugging_enabled]"' .
			'name="perfecty_push[widget_debugging_enabled]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the hide bell after the user has been subscribed
	 *
	 * @since 1.1.3
	 */
	public function print_widget_hide_bell_after_subscribe() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['widget_hide_bell_after_subscribe'] ) ? esc_attr( $options['widget_hide_bell_after_subscribe'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[widget_hide_bell_after_subscribe]"' .
			'name="perfecty_push[widget_hide_bell_after_subscribe]" %s class="perfecty-push-options-dialog-group"/>',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the visits to display prompt option
	 *
	 * @since 1.3.0
	 */
	public function print_visits_to_display_prompt() {
		$options     = get_option( 'perfecty_push' );
		$value       = isset( $options['visits_to_display_prompt'] ) && $options['visits_to_display_prompt'] ? esc_attr( $options['visits_to_display_prompt'] ) : '';
		$placeholder = ! $value ? '0 (Immediately)' : '';

		printf(
			'<input type="text" id="perfecty_push[visits_to_display_prompt]"' .
			'name="perfecty_push[visits_to_display_prompt]" value="%s" placeholder="%s"/>',
			esc_html( $value ),
			esc_html__( $placeholder, 'perfecty-push-notifications' )
		);
	}

	/**
	 * Print the ask permissions directly option
	 *
	 * @since 1.1.3
	 */
	public function print_widget_ask_permissions_directly() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['widget_ask_permissions_directly'] ) ? esc_attr( $options['widget_ask_permissions_directly'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[widget_ask_permissions_directly]"' .
			'name="perfecty_push[widget_ask_permissions_directly]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the logs driver setting
	 *
	 * @since 1.6.0
	 */
	public function print_log_driver() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['log_driver'] ) ? esc_attr( $options['log_driver'] ) : 'errorlog';

		$print_selected = function( $val ) use ( $value ) {
			return $val == $value ? 'selected' : '';
		};
		printf(
			'<select name="perfecty_push[log_driver]" id="perfecty_push[log_driver]">' .
			'<option value="errorlog" ' . $print_selected( 'errorlog' ) . '>PHP - error_log()</option>' .
			'<option value="db"' . $print_selected( 'db' ) . '>Database</option>' .
			'</select>'
		);
	}

	/**
	 * Print the logs level setting
	 *
	 * @since 1.6.0
	 */
	public function print_log_level() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['log_level'] ) ? esc_attr( $options['log_level'] ) : 'error';

		$print_selected = function( $val ) use ( $value ) {
			return $val == $value ? 'selected' : '';
		};
		printf(
			'<select name="perfecty_push[log_level]" id="perfecty_push[log_level]">' .
			'<option value="error" ' . $print_selected( 'error' ) . '>Error</option>' .
			'<option value="warning"' . $print_selected( 'warning' ) . '>Warning</option>' .
			'<option value="info"' . $print_selected( 'info' ) . '>Info</option>' .
			'<option value="debug"' . $print_selected( 'debug' ) . '>Debug</option>' .
			'</select>'
		);
	}

	/**
	 * Print the default notification icon setting
	 *
	 * @since 1.3.0
	 */
	public function print_notifications_default_icon() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['notifications_default_icon'] ) ? esc_attr( $options['notifications_default_icon'] ) : '';

		printf(
			'<div class="perfecty-push-default-icon-preview-container"><img src="%s" class="perfecty-push-default-icon-preview"/></div>' .
			'<input type="button" id="perfecty-push-default-icon-select" class="button" value="%s"/>' .
			'<input type="hidden" id="perfecty_push[notifications_default_icon]"' .
			'name="perfecty_push[notifications_default_icon]" value="%s"/>',
			wp_get_attachment_url( $value ),
			esc_html__( 'Select image', 'perfecty-push-notifications' ),
			esc_html( $value )
		);
	}

	/**
	 * Print the enable segmentation setting
	 *
	 * @since 1.1.3
	 */
	public function print_segmentation_enabled() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['segmentation_enabled'] ) ? esc_attr( $options['segmentation_enabled'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[segmentation_enabled]"' .
			'name="perfecty_push[segmentation_enabled]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the unregister_conflicts option
	 *
	 * @since 1.0.0
	 */
	public function print_unregister_conflicts() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['unregister_conflicts'] ) ? esc_attr( $options['unregister_conflicts'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[unregister_conflicts]"' .
			'name="perfecty_push[unregister_conflicts]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the unregister_conflicts_expression option
	 *
	 * @since 1.3.0
	 */
	public function print_unregister_conflicts_expression() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['unregister_conflicts_expression'] ) ? esc_attr( $options['unregister_conflicts_expression'] ) : '';

		printf(
			'<div>Default expression:</div><div class="perfecty-push-options-unregister-conflicts-regex">%s</div>' .
			'<textarea id="perfecty_push[unregister_conflicts_expression]"' .
			'name="perfecty_push[unregister_conflicts_expression]" class="perfecty-push-options-unregister-conflicts-group" ' .
			'placeholder="%s">%s</textarea>' .
			'<div><a href="%s" target="_blank">%s</a></div>',
			esc_html( PERFECTY_PUSH_UNREGISTER_CONFLICTS_EXPRESSION ),
			esc_html__( 'Custom JS Regular Expression', 'perfecty-push-notifications' ),
			esc_html( $value ),
			'https://docs.perfecty.org/wp/conflict-resolution/',
			esc_html__( 'More information', 'perfecty-push-notifications' )
		);
	}

	/**
	 * Print the service_worker_scope option
	 *
	 * @since 1.0.7
	 */
	public function print_service_worker_scope() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['service_worker_scope'] ) ? esc_attr( $options['service_worker_scope'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[service_worker_scope]"' .
			'name="perfecty_push[service_worker_scope]" value="%s" />',
			esc_html( $value )
		);
	}

	/**
	 * Print the notifications_interaction_required option
	 *
	 * @since 1.3.0
	 */
	public function print_notifications_interaction_required() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['notifications_interaction_required'] ) ? esc_attr( $options['notifications_interaction_required'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[notifications_interaction_required]"' .
			'name="perfecty_push[notifications_interaction_required]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the dialog_title option
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_title() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['dialog_title'] ) && ! empty( $options['dialog_title'] ) ? esc_attr( $options['dialog_title'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[dialog_title]"' .
			'name="perfecty_push[dialog_title]" value="%s" placeholder="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value ),
			PERFECTY_PUSH_OPTIONS_DIALOG_TITLE
		);
	}

	/**
	 * Print the dialog_submit option
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_submit() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['dialog_submit'] ) && ! empty( $options['dialog_submit'] ) ? esc_attr( $options['dialog_submit'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[dialog_submit]"' .
			'name="perfecty_push[dialog_submit]" value="%s" placeholder="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value ),
			PERFECTY_PUSH_OPTIONS_DIALOG_CONTINUE
		);
	}

	/**
	 * Print the dialog_cancel option
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_cancel() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['dialog_cancel'] ) && ! empty( $options['dialog_cancel'] ) ? esc_attr( $options['dialog_cancel'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[dialog_cancel]"' .
			'name="perfecty_push[dialog_cancel]" value="%s" placeholder="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value ),
			PERFECTY_PUSH_OPTIONS_DIALOG_CANCEL
		);
	}

	/**
	 * Print the settings_title option
	 *
	 * @since 1.0.0
	 */
	public function print_settings_title() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['settings_title'] ) && ! empty( $options['settings_title'] ) ? esc_attr( $options['settings_title'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[settings_title]"' .
			'name="perfecty_push[settings_title]" value="%s" placeholder="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value ),
			PERFECTY_PUSH_OPTIONS_SETTINGS_TITLE
		);
	}

	/**
	 * Print the settings_opt_in option
	 *
	 * @since 1.0.0
	 */
	public function print_settings_opt_in() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['settings_opt_in'] ) && ! empty( $options['settings_opt_in'] ) ? esc_attr( $options['settings_opt_in'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[settings_opt_in]"' .
			'name="perfecty_push[settings_opt_in]" value="%s" placeholder="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value ),
			PERFECTY_PUSH_OPTIONS_SETTINGS_OPT_IN
		);
	}

	/**
	 * Print the settings_update_error option
	 *
	 * @since 1.0.0
	 */
	public function print_settings_update_error() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['settings_update_error'] ) && ! empty( $options['settings_update_error'] ) ? esc_attr( $options['settings_update_error'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[settings_update_error]"' .
			'name="perfecty_push[settings_update_error]" value="%s" placeholder="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value ),
			PERFECTY_PUSH_OPTIONS_SETTINGS_UPDATE_ERROR
		);
	}

	/**
	 * Print the metabox options section
	 *
	 * @since 1.3.0
	 */
	public function print_metabox_section() {
		print esc_html__( 'Configure how to send notifications after publishing a Post.', 'perfecty-push-notifications' );
	}

	/**
	 * Print the check send on publish by default
	 *
	 * @since 1.3.0
	 */
	public function print_default_send_on_publish() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['check_send_on_publish'] ) ? esc_attr( $options['check_send_on_publish'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[check_send_on_publish]"' .
			'name="perfecty_push[check_send_on_publish]" %s/>',
			esc_html( $enabled )
		);
	}

	/**
	 * Get first image URL in post content
	 *
	 * @since 1.0.8
	 *
	 * @return string $thumbnail_url on success, '' on failure
	 */
	public function get_first_image_url( $post ) {
		$content = $post->post_content;
		$regex   = '/src="([^"]*)"/';
		preg_match_all( $regex, $content, $matches );
		$matches = array_reverse( $matches );
		// this is the image url of the first img embedded in content.
		$img_url = $matches[0][0] ?? '';

		// this is the image post id.
		$post_img_id = $this->get_attachment_id( $img_url );

		if ( $post_img_id !== 0 ) {
			// this is an array related to the thumbnail of the first image. If post-thumbnail size is not set, it returns original image.
			$img_thumb_url = wp_get_attachment_image_src( $post_img_id, $size = 'post-thumbnail', $icon = false );
		} else {
			$img_thumb_url[0] = '';
		}
		// we return the URL of the thumbnail.
		return $img_thumb_url[0];
	}

	/**
	 * Get an attachment ID given a URL.
	 * https://wordpress.stackexchange.com/questions/6645/turn-a-url-into-an-attachment-post-id/7094#7094
	 *
	 * @param string $url
	 *
	 * @return int Attachment ID on success, 0 on failure
	 */
	function get_attachment_id( $url ) {
		$attachment_id = 0;
		$dir           = wp_upload_dir();
		if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
			$file       = basename( $url );
			$query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array(
					array(
						'value'   => $file,
						'compare' => 'LIKE',
						'key'     => '_wp_attachment_metadata',
					),
				),
			);

			$query = new WP_Query( $query_args );
			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post_id ) {
					$meta                = wp_get_attachment_metadata( $post_id );
					$original_file       = basename( $meta['file'] );
					$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
					if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
						$attachment_id = $post_id;
						break;
					}
				}
			}
		}
		return $attachment_id;
	}

	/**
	 * Print the settings_send_welcome_message option
	 *
	 * @since 1.4.0
	 */
	public function print_settings_send_welcome_message() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['settings_send_welcome_message'] ) ? esc_attr( $options['settings_send_welcome_message'] ) : 0;

		$enabled = $value ? 'checked="checked"' : '';

		printf(
			'<input type="checkbox" id="perfecty_push[settings_send_welcome_message]"' .
			'name="perfecty_push[settings_send_welcome_message]" %s />',
			esc_html( $enabled )
		);
	}

	/**
	 * Print the settings_welcome_message option
	 *
	 * @since 1.4.0
	 */
	public function print_settings_welcome_message() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['settings_welcome_message'] ) && ! empty( $options['settings_welcome_message'] ) ? esc_attr( $options['settings_welcome_message'] ) : PERFECTY_PUSH_OPTIONS_SETTINGS_WELCOME_MESSAGE;

		printf(
			'<input type="text" id="perfecty_push[settings_welcome_message]"' .
			'name="perfecty_push[settings_welcome_message]" value="%s" class="perfecty-push-options-dialog-group"/>',
			esc_html( $value )
		);
	}
}
