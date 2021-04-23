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

use Ramsey\Uuid\Uuid;

class Perfecty_Push_Users {
	/**
	 * Register the user
	 *
	 * @since 1.0.0
	 */
	public function register( $data ) {
		// Request
		$user      = $data['user'] ?? null;
		$user_id   = $data['user_id'] ?? null;
		$remote_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

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
		$validation = $this->validate( $endpoint, $key_auth, $key_p256dh, $remote_ip, $user_id );
		if ( $validation === true ) {
			// filter data
			$endpoint   = esc_url( $endpoint );
			$key_auth   = sanitize_text_field( $key_auth );
			$key_p256dh = sanitize_text_field( $key_p256dh );
			$remote_ip  = sanitize_text_field( $remote_ip );
			$user_id    = sanitize_text_field( $user_id );

			$user = Perfecty_Push_Lib_Db::get_user_by( $user_id, $key_auth, $key_p256dh );
			if ( $user ) {
				$user->endpoint   = $endpoint;
				$user->key_auth   = $key_auth;
				$user->key_p256dh = $key_p256dh;
				$user->remote_ip  = $remote_ip;
				$result           = Perfecty_Push_Lib_Db::update_user( $user );
				if ( $result === false ) {
					// Could not update the user
					return new WP_Error( 'failed_update', __( 'Could not update the user', 'perfecty-push-notifications' ), array( 'status' => 500 ) );
				}
			} else {
				$result = Perfecty_Push_Lib_Db::create_user( $endpoint, $key_auth, $key_p256dh, $remote_ip );
				if ( $result === false ) {
					// Could not subscribe
					return new WP_Error( 'failed_create', __( 'Could not subscribe the user', 'perfecty-push-notifications' ), array( 'status' => 500 ) );
				}
				$user = Perfecty_Push_Lib_Db::get_user( $result );
			}

			// The user was registered
			$response = array(
				'uuid'      => $user->uuid,
				'is_active' => (bool) $user->is_active,
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
				'uuid'      => $user->uuid,
				'is_active' => (bool) $user->is_active,
			);
		}
		return (object) $result;
	}

	/**
	 * Change the user preferences
	 *
	 * @since 1.0.0
	 */
	public function update_preferences( $data ) {
		$is_active = $data['is_active'] ?? null;
		$user_id   = $data['user_id'] ?? null;

		// Validate the nonce.
		$nonce = isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'wp_rest' ) === false ) {
			$this->terminate();
		}

		$validation = $this->validate_set_user_active( $is_active, $user_id );
		if ( $validation !== true ) {
			return new WP_Error( 'bad_request', $validation, array( 'status' => 400 ) );
		}

		$user = Perfecty_Push_Lib_Db::get_user_by_uuid( $user_id );
		if ( $user == null ) {
			return new WP_Error( 'bad_request', __( 'user id not found', 'perfecty-push-notifications' ), array( 'status' => 404 ) );
		}
		$result = Perfecty_Push_Lib_Db::set_user_active( $user->id, $is_active );

		if ( $result === false ) {
			return new WP_Error( 'failed_update', __( 'Could not change the user', 'perfecty-push-notifications' ), array( 'status' => 500 ) );
		} else {
			$response = array(
				'is_active' => $is_active,
			);
			return (object) $response;
		}
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

	private function validate( $endpoint, $key_auth, $key_p256dh, $remote_ip, $user_id ) {
		if ( ! $endpoint ) {
			return __( 'No endpoint was provided in the request', 'perfecty-push-notifications' );
		}
		if ( ! $key_auth ) {
			return __( 'Missing the auth key', 'perfecty-push-notifications' );
		}
		if ( ! $key_p256dh ) {
			return __( 'Missing the public p256dh key', 'perfecty-push-notifications' );
		}
		if ( ! $remote_ip ) {
			return __( 'Unknown Ip address', 'perfecty-push-notifications' );
		}
		if ( $user_id && ! Uuid::isValid( $user_id ) ) {
			return __( 'The user id is not a valid uuid', 'perfecty-push-notifications' );
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

	private function validate_set_user_active( $is_active, $user_id ) {
		if ( null === $is_active || null === $user_id ) {
			return __( 'Missing parameters', 'perfecty-push-notifications' );
		}
		if ( false !== $is_active && true !== $is_active ) {
			return __( 'is_active must be a boolean', 'perfecty-push-notifications' );
		}
		if ( ! Uuid::isValid( $user_id ) ) {
			return __( 'Invalid player ID', 'perfecty-push-notifications' );
		}

		return true;
	}

	private function validate_get_user( $user_id ) {
		if ( ! Uuid::isValid( $user_id ) ) {
			return __( 'Invalid player ID', 'perfecty-push-notifications' );
		}

		return true;
	}
}
