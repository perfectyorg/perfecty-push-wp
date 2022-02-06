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
		$options = get_option( 'perfecty_push', array() );

		$icon_url = isset( $options['notifications_default_icon'] ) && ! empty( $options['notifications_default_icon'] ) ? wp_get_attachment_url( $options['notifications_default_icon'] ) : '';

		$require_interaction = isset( $options['notifications_interaction_required'] ) && $options['notifications_interaction_required'] == 1;

		if ( ! $title ) {
			$title = get_bloginfo( 'name' );
		}
		if ( ! $url_to_open ) {
			$url_to_open = get_site_url();
		}
		if ( ! $image ) {
			$image = '';
		}

		$utm = isset( $options['segmentation_tracking_utm'] ) ? $options['segmentation_tracking_utm'] : '';
		if ( '' !== $utm ) {
			$prefix      = strpos( $url_to_open, '?' ) === false ? '?' : '&';
			$url_to_open = $url_to_open . $prefix . $utm;
		}

		return array(
			'title'               => substr( stripslashes( $title ), 0, 250 ),
			'body'                => substr( stripslashes( $body ), 0, 1750 ),
			'icon'                => $icon_url,
			'image'               => $image,
			'require_interaction' => $require_interaction,
			'extra'               => array(
				'url_to_open' => $url_to_open,
			),
		);
	}
}
