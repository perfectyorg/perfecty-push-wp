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
 * Users registration
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/public
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */

use Perfecty_Push_External_Uuid as Uuid;
use Perfecty_Push_Lib_Log as Log;

class Perfecty_Push_Users {
	/**
	 * Register the user
	 *
	 * @since 1.0.0
	 */
	public function register( $data ) {
		// Request
		$user       = $data['user'] ?? null;
		$user_id    = $data['user_id'] ?? null;
		$first_time = $data['first_time'] ?? null;
		$remote_ip  = $this->get_remote_ip();

		// Validate the nonce
		$nonce = isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'wp_rest' ) === false ) {
			$this->terminate();
		}

		// Extract the data
		$res        = $this->extract_data( $user );
		$endpoint   = $res[0];
		$key_auth   = $res[1];
		$key_p256dh = $res[2];

		// Process request
		$validation = $this->validate( $endpoint, $key_auth, $key_p256dh, $remote_ip, $user_id, $first_time );
		if ( $validation === true ) {
			// filter data
			$endpoint   = esc_url( $endpoint );
			$key_auth   = sanitize_text_field( $key_auth );
			$key_p256dh = sanitize_text_field( $key_p256dh );
			$remote_ip  = sanitize_text_field( $remote_ip );
			$user_id    = sanitize_text_field( $user_id );
			$first_time = sanitize_text_field( $first_time );
			$wp_user_id = is_user_logged_in() ? get_current_user_id() : null;

			// check if this is a valid subscription
			try {
				Perfecty_Push_External_Webpush::subscription( $endpoint, $key_p256dh, $key_auth );
			} catch ( Exception $e ) {
				Log::error( 'Invalid subscription: ' . $e->getMessage() );
				return new WP_Error( 'validation_error', __( 'Invalid subscription parameters', 'perfecty-push-notifications' ), array( 'status' => 400 ) );
			}

			$user = Perfecty_Push_Lib_Db::get_user_by( $user_id, $key_auth, $key_p256dh );
			if ( $user ) {
				$user->endpoint   = $endpoint;
				$user->key_auth   = $key_auth;
				$user->key_p256dh = $key_p256dh;
				$user->remote_ip  = $remote_ip;
				$user->wp_user_id = $wp_user_id;
				$result           = Perfecty_Push_Lib_Db::update_user( $user );
				if ( $result === false ) {
					// Could not update the user
					return new WP_Error( 'failed_update', __( 'Could not update the user', 'perfecty-push-notifications' ), array( 'status' => 500 ) );
				}
			} else {
				$result = Perfecty_Push_Lib_Db::create_user( $endpoint, $key_auth, $key_p256dh, $remote_ip, $wp_user_id );
				if ( $result === false ) {
					// Could not subscribe
					return new WP_Error( 'failed_create', __( 'Could not subscribe the user', 'perfecty-push-notifications' ), array( 'status' => 500 ) );
				}
				$user = Perfecty_Push_Lib_Db::get_user( $result );

				// Send a confirmation notification.
				$options              = get_option( 'perfecty_push' );
				$send_welcome_message = isset( $options['settings_send_welcome_message'] ) && $options['settings_send_welcome_message'] == 1;
				if ( $first_time && $send_welcome_message ) {
					$message = isset( $options['settings_welcome_message'] ) && ! empty( $options['settings_welcome_message'] ) ? esc_attr( $options['settings_welcome_message'] ) : PERFECTY_PUSH_OPTIONS_SETTINGS_WELCOME_MESSAGE;
					$payload = Perfecty_Push_Lib_Payload::build( html_entity_decode( $message, ENT_QUOTES ) );
					Perfecty_Push_Lib_Push_Server::send_notification( json_encode( $payload ), array( $user ) );
				}
			}

			// The user was registered
			$response = array(
				'uuid' => $user->uuid,
			);
			return (object) $response;
		} else {
			error_log( $validation );
			return new WP_Error( 'validation_error', $validation, array( 'status' => 400 ) );
		}
	}

	/**
	 * Get the user information
	 *
	 * @since 1.0.0
	 */
	public function get_user( $data ) {
		$user_id = $data['user_id'] ?? null;

		// Validate the nonce.
		$nonce = isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'wp_rest' ) === false ) {
			$this->terminate();
		}

		$validation = $this->validate_get_user( $user_id );
		if ( $validation !== true ) {
			return new WP_Error( 'bad_request', $validation, array( 'status' => 400 ) );
		}

		$user   = Perfecty_Push_Lib_Db::get_user_by_uuid( $user_id );
		$result = array();
		if ( $user !== null ) {
			$result = array(
				'uuid' => $user->uuid,
			);
		}
		return (object) $result;
	}

	/**
	 * Remove the user subscription
	 *
	 * @since 1.2.0
	 */
	public function unregister( $data ) {
		$user_id = $data['user_id'] ?? null;

		// Validate the nonce.
		$nonce = isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'wp_rest' ) === false ) {
			$this->terminate();
		}

		$validation = $this->validate_delete( $user_id );
		if ( $validation !== true ) {
			return new WP_Error( 'bad_request', $validation, array( 'status' => 400 ) );
		}

		$user = Perfecty_Push_Lib_Db::get_user_by_uuid( $user_id );
		if ( $user == null ) {
			return new WP_Error( 'bad_request', __( 'User id not found', 'perfecty-push-notifications' ), array( 'status' => 404 ) );
		}
		$result = Perfecty_Push_Lib_Db::delete_users( array( $user->id ) );

		if ( $result === false ) {
			return new WP_Error( 'failed_delete', __( 'Could not delete the user', 'perfecty-push-notifications' ), array( 'status' => 500 ) );
		} else {
			return new WP_REST_Response( null, 200 );
		}
	}

	private function get_remote_ip() {
		$options              = get_option( 'perfecty_push', array() );
		$segmentation_enabled = isset( $options['segmentation_enabled'] ) && $options['segmentation_enabled'] == 1;
		return $segmentation_enabled && isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}

	private function extract_data( $user ) {
		$endpoint   = isset( $user['endpoint'] ) ? $user['endpoint'] : '';
		$key_auth   = '';
		$key_p256dh = '';

		if ( isset( $user['keys'] ) ) {
			// it follows the standard: https://www.w3.org/TR/2018/WD-push-api-20181026/
			$keys       = $user['keys'];
			$key_auth   = isset( $keys['auth'] ) ? $keys['auth'] : '';
			$key_p256dh = isset( $keys['p256dh'] ) ? $keys['p256dh'] : '';
		}

		return array( $endpoint, $key_auth, $key_p256dh );
	}

	private function validate( $endpoint, $key_auth, $key_p256dh, $remote_ip, $user_id, $first_time ) {
		if ( ! $endpoint ) {
			return __( 'No endpoint was provided in the request', 'perfecty-push-notifications' );
		}
		if ( ! $key_auth ) {
			return __( 'Missing the auth key', 'perfecty-push-notifications' );
		}
		if ( ! $key_p256dh ) {
			return __( 'Missing the public p256dh key', 'perfecty-push-notifications' );
		}
		if ( $remote_ip && ! filter_var( $remote_ip, FILTER_VALIDATE_IP ) ) {
			return __( 'Unknown Ip address', 'perfecty-push-notifications' );
		}
		if ( $user_id && ! Uuid::isValid( $user_id ) ) {
			return __( 'The user id is not a valid uuid', 'perfecty-push-notifications' );
		}
		if ( $first_time !== null && ! is_bool( $first_time ) ) {
			return __( 'The first time parameter is not valid', 'perfecty-push-notifications' );
		}

		// At this point everything is valid
		return true;
	}

	/**
	 * Terminates the program when the nonce is not valid
	 */
	public function terminate() {
		wp_die( -1, 403 );
	}

	private function validate_delete( $user_id ) {
		if ( ! Uuid::isValid( $user_id ) ) {
			return __( 'Invalid user ID', 'perfecty-push-notifications' );
		}

		return true;
	}

	private function validate_get_user( $user_id ) {
		if ( ! Uuid::isValid( $user_id ) ) {
			return __( 'Invalid user ID', 'perfecty-push-notifications' );
		}

		return true;
	}
}
