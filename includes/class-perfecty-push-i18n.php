<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/includes
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'perfecty-push',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
		
		// Define text constants after textdomain is loaded
		$this->define_text_constants();
	}

	/**
	 * Define translatable text constants.
	 *
	 * @since    1.6.5
	 */
	public function define_text_constants() {
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_DIALOG_TITLE' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_DIALOG_TITLE',
				esc_html__( 'Do you want to receive notifications?', 'perfecty-push-notifications' )
			);
		}
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_DIALOG_CONTINUE' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_DIALOG_CONTINUE',
				esc_html__( 'Continue', 'perfecty-push-notifications' )
			);
		}
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_DIALOG_CANCEL' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_DIALOG_CANCEL',
				esc_html__( 'Not now', 'perfecty-push-notifications' )
			);
		}
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_SETTINGS_TITLE' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_SETTINGS_TITLE',
				esc_html__( 'Notifications preferences', 'perfecty-push-notifications' )
			);
		}
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_SETTINGS_OPT_IN' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_SETTINGS_OPT_IN',
				esc_html__( 'I want to receive notifications', 'perfecty-push-notifications' )
			);
		}
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_SETTINGS_UPDATE_ERROR' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_SETTINGS_UPDATE_ERROR',
				esc_html__( 'Could not change the preference, try again', 'perfecty-push-notifications' )
			);
		}
		if ( ! defined( 'PERFECTY_PUSH_OPTIONS_SETTINGS_WELCOME_MESSAGE' ) ) {
			define(
				'PERFECTY_PUSH_OPTIONS_SETTINGS_WELCOME_MESSAGE',
				esc_html__( 'Congratulations, you\'re now subscribed!', 'perfecty-push-notifications' )
			);
		}
	}



}
