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

		return array(
			'title' => $title,
			'body'  => $body,
			'icon'  => $icon,
			'image' => $image,
			'extra' => array(
				'url_to_open' => $url_to_open,
			),
		);
	}
}
