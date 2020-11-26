<?php

use Ramsey\Uuid\Uuid;

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
 * Subscribers registration
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/public
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_Subscribers {
  /**
   * Register the subscriber
   *
   * @since 1.0.0
   */
  public function register ($data) {
    // Request
    $subscription = $data['subscription'] ?? null;
    $remote_ip = $_SERVER["REMOTE_ADDR"];
    
    // Validate the nonce
    if (check_ajax_referer('wp_rest', '_wpnonce', false) == false) {
      $this->terminate();
    }

    // Extract the data
    [$endpoint, $key_auth, $key_p256dh] = $this->extract_data($subscription);

    // Process request
    $validation = $this->validate($endpoint, $key_auth, $key_p256dh, $remote_ip);
    if ($validation === true){
      // filter data
      $endpoint = esc_url($endpoint);
      $key_auth = sanitize_text_field($key_auth);
      $key_p256dh = sanitize_text_field($key_p256dh);
      $remote_ip = sanitize_text_field($remote_ip);

      // store the subscription in the DB
      $result = Perfecty_Push_Lib_Db::store_subscription($endpoint, $key_auth, $key_p256dh, $remote_ip);

      if ($result == false) {
        // Could not subscribe
        return new WP_Error("failed_subscription", "Could not subscribe the user", array('status' => 500));
      }
      else {
        // The subscription was correct
        $subscription = Perfecty_Push_Lib_Db::get_subscription($result);
        $response = [
          'success' => true,
          'uuid' => $subscription->uuid
        ];
        return (object) $response;
      }
    } else {
      error_log($validation);
      return new WP_Error("validation_error", $validation, array('status' => 400));
    }
  }

  /**
   * Set the user active or inactive
   * 
   * @since 1.0.0
   */
  public function set_user_active($data) {
    $is_active = $data['is_active'] ?? null;
    $user_id = $data['user_id'] ?? null;

    // Validate the nonce
    if (check_ajax_referer('wp_rest', '_wpnonce', false) == false) {
      $this->terminate();
    }

    $validation = $this->validate_set_user_active($is_active, $user_id);
    if ($validation !== true) {
      return new WP_Error("bad request", $validation, array('status' => 400));
    }

    $subscription = Perfecty_Push_Lib_Db::get_subscription_by_uuid($user_id);
    if ($subscription == null) {
      return new WP_Error("bad request", "user id not found", ['status' => 404]);
    }
    $result = Perfecty_Push_Lib_Db::set_subscription_active($subscription->id, $is_active);

    if ($result === false) {
      return new WP_Error("failed_update", "Could not change the subscription", array('status' => 500));
    } else {
      $response = [
        'success' => true,
        'is_active' => $is_active
      ];
      return (object) $response;
    }
  }

  private function extract_data($subscription) {
    $endpoint = isset($subscription['endpoint']) ? $subscription['endpoint'] : "";
    $key_auth = '';
    $key_p256dh = '';

    if (isset($subscription['keys'])) {
      // it follows the standard: https://www.w3.org/TR/2018/WD-push-api-20181026/
      $keys = $subscription['keys'];
      $key_auth = isset($keys['auth']) ? $keys['auth'] : '';
      $key_p256dh = isset($keys['p256dh']) ? $keys['p256dh'] : '';
    }

    return [$endpoint, $key_auth, $key_p256dh];
  }

  private function validate($endpoint, $key_auth, $key_p256dh, $remote_ip) {
    if (!$endpoint) return "No endpoint was provided in the request";
    if (!$key_auth) return "Missing the auth key";
    if (!$key_p256dh) return "Missing the public p256dh key";
    if (!$remote_ip) return "Unknown Ip address";

    // At this point everything is valid
    return true;
  }

  /**
   * Terminates the program when the nonce is not valid
   */
  public function terminate() {
    wp_die(-1, 403);
  }

  private function validate_set_user_active($is_active, $user_id) {
    if ($is_active === null || $user_id === null) return "Missing parameters";
    if ($is_active != false && $is_active != true) {
      return "is_active must be a boolean";
    }
    if (!Uuid::isValid($user_id)) return "Invalid player ID";

    return true;
  }
}