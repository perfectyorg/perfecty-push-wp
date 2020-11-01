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
    check_ajax_referer('wp_rest', '_wpnonce');

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

      if ($result !== false) {
        // The subscription was correct
        return (object) ['result' => true];
      }
      else{
        // Could not subscribe
        return new WP_Error("failed_subscription", "Could not subscribe the user", array('status' => 400));
      }
    } else {
      log_error($validation);
      return new WP_Error("validation_error", $validation, array('status' => 400));
    }
  }

  private function extract_data($subscription) {
    $endpoint = $subscription['endpoint'];
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
}