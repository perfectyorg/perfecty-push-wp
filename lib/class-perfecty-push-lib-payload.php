<?php

/***
 * Paylod builder
 */
class Perfecty_Push_Lib_Payload {

	/**
	 * Build the payload to be send as a notification
	 *
	 * @param string $body Notification content
	 * @param string $title Title
	 * @param string $image Image to show
	 * @param string $url_to_open Url to open
	 * @return array payload
	 */
	public static function build( $body, $title = '', $image = '', $url_to_open = '' ) {
		$icon = get_site_icon_url();
		if ( ! $title ) {
			$title = get_bloginfo( 'name' );
		}
		if ( ! $url_to_open ) {
			$url_to_open = get_site_url();
		}
		if ( ! $image ) {
			$image = '';
		}
		$options             = get_option( 'perfecty_push', array() );
		$require_interaction = isset( $options['notifications_interaction_required'] ) && $options['notifications_interaction_required'] == 1;

		return array(
			'title'               => stripslashes( $title ),
			'body'                => stripslashes( $body ),
			'icon'                => $icon,
			'image'               => $image,
			'require_interaction' => $require_interaction,
			'extra'               => array(
				'url_to_open' => $url_to_open,
			),
		);
	}
}
