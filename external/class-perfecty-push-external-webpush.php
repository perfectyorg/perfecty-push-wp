<?php

/**
 * Class Perfecty_Push_External_Webpush
 *
 * WebPush external wrapper
 */
class Perfecty_Push_External_Webpush {

	/**
	 * Return a new webpush object
	 *
	 * @return WebPush
	 * @throws ErrorException
	 */
	public static function get( $auth = array(), $defaultOptions = array() ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		return new Minishlink\WebPush\WebPush( $auth, $defaultOptions );
	}

	/**
	 * Return a new webpush subscription
	 *
	 * @return Subscription
	 * @throws ErrorException
	 */
	public static function subscription( $endpoint, $publicKey, $authToken ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		return new Minishlink\WebPush\Subscription( $endpoint, $publicKey, $authToken );
	}

	/**
	 * Return a new set of vapid keys
	 *
	 * @return array
	 * @throws ErrorException
	 */
	public static function createVapidKeys() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		return Minishlink\WebPush\VAPID::createVapidKeys();
	}
}
