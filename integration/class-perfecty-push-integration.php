<?php
/**
 * The integration interface between Perfecty Push and external WordPress plugins.
 *
 * @link       https://github.com/rwngallego
 * @since      1.2.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/integration
 */

/**
 * Perfecty Push integration
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/integration
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */

class Perfecty_Push_Integration {

	/**
	 * Send one notification to the WordPress user specified by $user_id
	 *
	 * @param int    $wp_user_id WordPress User Id
	 * @param string $message Message to be sent
	 * @param string $title Title of the message (default: site name)
	 * @param string $image_url Url of the image to show (default: none)
	 * @param string $url_to_open Url to open (default: site url)
	 * @return array Array with [total, succeeded]
	 * @throws Exception
	 * @since 1.2.0
	 */
	public function notify( $wp_user_id, $message, $title = '', $image_url = '', $url_to_open = '' ) {
		$payload = Perfecty_Push_Lib_Payload::build( $message, $title, $image_url, $url_to_open );
		return Perfecty_Push_Lib_Push_Server::notify( $wp_user_id, $payload );
	}

	/**
	 * Schedule a broadcast notification to all the users.
	 * If succeeded it will return the notification id
	 *
	 * @param string $message Message to be sent
	 * @param string $title Title of the message (default: site name)
	 * @param string $image_url Url of the image to show (default: none)
	 * @param string $url_to_open Url to open (default: site url)
	 * @return int $notification_id if success, false otherwise
	 * @throws Exception
	 * @since 1.2.0
	 */
	public function broadcast( $message, $title = '', $image_url = '', $url_to_open = '' ) {
		$payload = Perfecty_Push_Lib_Payload::build( $message, $title, $image_url, $url_to_open );
		return Perfecty_Push_Lib_Push_Server::broadcast( $payload );
	}
}
