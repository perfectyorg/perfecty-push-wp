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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/perfecty-push-admin.js', array( 'jquery' ), $this->version, false );

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
			array($this, 'print_dashboard_page'));

    add_submenu_page(
      'perfecty-push',
      'Dashboard',
      'Dashboard',
      'manage_options',
      'perfecty-push',
			array($this, 'print_dashboard_page'));

    add_submenu_page(
      'perfecty-push',
      'Send notification',
      'Send notification',
      'manage_options',
      'perfecty-push-send-notification',
      array($this, 'print_send_notification_page'));

    add_submenu_page(
      'perfecty-push',
      'Settings',
      'Settings',
      'manage_options',
      'perfecty-push-options',
			array($this, 'print_options_page'));

    add_submenu_page(
      'perfecty-push',
      'About',
      'About',
      'manage_options',
      'perfecty-push-about',
      array($this, 'print_about_page'));

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
      array($this, 'sanitize')
    );

    add_settings_section(
      'perfecty_push_fab_settings', // id
      'Change the appearance', // title
      array($this, 'print_fab_section'), // callback
      'perfecty-push-options' // page
    );

    add_settings_field(
      'fab_title', // id
      'Title', // title
      array($this, 'print_fab_title'), // callback
      'perfecty-push-options', // page
      'perfecty_push_fab_settings' // section
		);

    add_settings_field(
      'fab_submit', // id
      'Continue Button', // title
      array($this, 'print_fab_submit'), // callback
      'perfecty-push-options', // page
      'perfecty_push_fab_settings' // section
		);

    add_settings_field(
      'fab_cancel', // id
      'Cancel Button', // title
      array($this, 'print_fab_cancel'), // callback
      'perfecty-push-options', // page
      'perfecty_push_fab_settings' // section
		);

    add_settings_section(
      'perfecty_push_self_hosted_settings', // id
      'Self-hosted server', // title
      array($this, 'print_self_hosted_section'), // callback
      'perfecty-push-options' // page
		);

    add_settings_field(
      'vapid_public_key', // id
      'Vapid Public Key', // title
      array($this, 'print_vapid_public_key'), // callback
      'perfecty-push-options', // page
      'perfecty_push_self_hosted_settings' // section
    );
    
    add_settings_field(
      'server_url', // id
      'Server Url', // title
      array($this, 'print_server_url'), // callback
      'perfecty-push-options', // page
      'perfecty_push_self_hosted_settings' // section
    );
  }

	/**
   * Renders the dashboard page
   *
   * @since 1.0.0
   */
  public function print_dashboard_page() {
    require_once plugin_dir_path(__FILE__) . 'partials/perfecty-push-admin-dashboard.php';
	}

	/**
   * Renders the options page
   *
   * @since 1.0.0
   */
  public function print_options_page() {
    require_once plugin_dir_path(__FILE__) . 'partials/perfecty-push-admin-options.php';
  }

	/**
   * Renders the send notification page
   *
   * @since 1.0.0
   */
  public function print_send_notification_page() {
    $message = '';
    $notice = '';

    $default = array(
      'title'                    => '',
      'message'                  => '',
    );

    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'perfecty_push_send_notification')) {

      $item = shortcode_atts($default, $_REQUEST);

      $validation_result = $this->validate_notification_message($item);
      if ($validation_result === true) {
        // filter
        $item['title'] = sanitize_text_field($item['title']);
        $item['message'] = sanitize_textarea_field($item['message']);

        $payload = json_encode([
          "title" => $item['title'],
          "message" => $item['message']
        ]);

        // send notification
        $result = Perfecty_Push_Lib_Push_Server::send_notification($payload);
        if (is_array($result)) {
          [$total, $succeded] = $result;
          $message = "The message was sent to $succeded subscribers out of $total.";
        } else {
          $notice = "Could not send the message, error: $result";
        }
      } else {
        $notice = $validation_result;
      }
    }
    else {
      $item = $default;
    }

    add_meta_box(
      'perfecty_push_send_notification_meta_box',
      'Notification details',
      array($this, 'print_send_notification_metabox'),
      'perfecty-push-send-notification',
      'normal');

    require_once plugin_dir_path(__FILE__) . 'partials/perfecty-push-admin-send-notification.php';
  }

  /**
   * Validates the notification details
   *
   * @param $item Contains the entry
   */
  public function validate_notification_message($item) {
    $messages = array();

    if (empty($item['title'])) $messages[] = 'The title is required';

    if (empty($item['message'])) $messages[] = 'The message is required';

    if (empty($messages)) {
      return true;
    } else {
      return implode('<br />', $messages);
    }
  }

  /**
   * Renders the send notification metabox
   *
   * @since 1.0.0
   */
  public function print_send_notification_metabox($item) {
    require_once plugin_dir_path(__FILE__) . 'partials/send-notification-metabox.php';
  }

	/**
   * Renders the about page
   *
   * @since 1.0.0
   */
  public function print_about_page() {
    require_once plugin_dir_path(__FILE__) . 'partials/perfecty-push-admin-about.php';
	}

  /**
   * Sanitize the settings
   *
   * @param $input Contains the settings
   */
  public function sanitize($input) {
    $new_input = array();
    if( isset( $input['vapid_public_key'] ) ){
      $new_input['vapid_public_key'] = sanitize_text_field( $input['vapid_public_key'] );
    }
    if( isset( $input['server_url'] ) ){
      $new_input['server_url'] = sanitize_text_field( $input['server_url'] );
		}

    return $new_input;
	}

  /**
   * Print the general section info
   *
   * @since 1.0.0
   */
  public function print_fab_section() {
    print 'Change the messages shown in the notification dialog.';
	}

  /**
   * Print the self hosted section info
   *
   * @since 1.0.0
   */
  public function print_self_hosted_section() {
    print 'Configure how to connect your website with your self-hosted Perfecty Push Server.';
  }
    
  /**
   * Print the Vapid public key
   *
   * @since 1.0.0
   */
  public function print_vapid_public_key() {
    $options = get_option('perfecty_push');
    $value = isset($options['vapid_public_key']) ? esc_attr($options['vapid_public_key']) : '';

    printf(
      '<input type="text" id="perfecty_push[vapid_public_key]"' .
      'name="perfecty_push[vapid_public_key]" value="%s" />', $value
    );
  }

  /**
   * Print the server_url option
   *
   * @since 1.0.0
   */
  public function print_server_url() {
    $options = get_option('perfecty_push');
    $value = isset($options['server_url']) ? esc_attr($options['server_url']) : '';

    printf(
      '<input type="text" id="perfecty_push[server_url]"' .
      'name="perfecty_push[server_url]" value="%s" placeholder="127.0.0.1:8777"/>', $value
    );
	}

  /**
   * Print the fab_title option
   *
   * @since 1.0.0
   */
  public function print_fab_title() {
    $options = get_option('perfecty_push');
    $value = isset($options['fab_title']) ? esc_attr($options['fab_title']) : '';

    printf(
      '<input type="text" id="perfecty_push[fab_title]"' .
      'name="perfecty_push[fab_title]" value="%s" />', $value
    );
	}

  /**
   * Print the fab_submit option
   *
   * @since 1.0.0
   */
  public function print_fab_submit() {
    $options = get_option('perfecty_push');
    $value = isset($options['fab_submit']) ? esc_attr($options['fab_submit']) : '';

    printf(
      '<input type="text" id="perfecty_push[fab_submit]"' .
      'name="perfecty_push[fab_submit]" value="%s" />', $value
    );
	}

  /**
   * Print the fab_cancel option
   *
   * @since 1.0.0
   */
  public function print_fab_cancel() {
    $options = get_option('perfecty_push');
    $value = isset($options['fab_cancel']) ? esc_attr($options['fab_cancel']) : '';

    printf(
      '<input type="text" id="perfecty_push[fab_cancel]"' .
      'name="perfecty_push[fab_cancel]" value="%s" />', $value
    );
  }
}
