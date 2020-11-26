<?php

use Ramsey\Uuid\Uuid;

/***
 * DB library
 */
class Perfecty_Push_Lib_Db {
  
  private static $allowed_subscriptions_fields = "id,uuid,endpoint,key_auth,key_p256dh,remote_ip,is_active";
  private static $allowed_notifications_fields = "id,payload,total,succeeded,last_cursor,batch_size,status,is_taken";

  public const NOTIFICATIONS_STATUS_SCHEDULED = "scheduled";
  public const NOTIFICATIONS_STATUS_FAILED = "failed";
  public const NOTIFICATIONS_STATUS_COMPLETED = "completed";

  private static function with_prefix($table) {
    global $wpdb;
    return $wpdb->prefix . $table;
  }

  private static function subscriptions_table() {
    return self::with_prefix('perfecty_push_subscriptions');
  }

  private static function notifications_table() {
    return self::with_prefix('perfecty_push_notifications');
  }

  /**
   * Creates the tables in the wordpress DB and register the DB version
   */
  public static function db_create() {
    global $wpdb;

    $perfecty_push_db_version = '1.0';

    # We need this for dbDelta() to work
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset = $wpdb->get_charset_collate();

    # We execute the queries per table
    $sql = "CREATE TABLE IF NOT EXISTS " . Perfecty_Push_Lib_Db::subscriptions_table() . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          uuid CHAR(36) NOT NULL,
          remote_ip VARCHAR(46) DEFAULT '',
          endpoint VARCHAR(500) NOT NULL UNIQUE,
          key_auth VARCHAR(100) NOT NULL UNIQUE,
          key_p256dh VARCHAR(100) NOT NULL UNIQUE,
          is_active TINYINT(1) DEFAULT 1 NOT NULL,
          disabled TINYINT(1) DEFAULT 0 NOT NULL,
          creation_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id),
          UNIQUE KEY `subscribers_uuid_uk` (`uuid`)
        ) $charset;";
    dbDelta( $sql );

    $sql = "CREATE TABLE IF NOT EXISTS " . Perfecty_Push_Lib_Db::notifications_table() . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          payload VARCHAR(500) NOT NULL,
          total INT(11) DEFAULT 0 NOT NULL,
          succeeded INT(11) DEFAULT 0 NOT NULL,
          last_cursor INT(11) DEFAULT 0 NOT NULL,
          batch_size INT(11) DEFAULT 0 NOT NULL,
          status VARCHAR(15) DEFAULT 'scheduled' NOT NULL,
          is_taken TINYINT(1) DEFAULT 0 NOT NULL,
          creation_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id)
        ) $charset;";
    dbDelta( $sql );

    add_option( 'perfecty_push_version', $perfecty_push_db_version);
  }

  /**
   * Store the subscription in the DB
   * 
   * @param $endpoint
   * @param $key_auth
   * @param $key_p256dh
   * @param $remote_ip
   * 
   * @return $uuid The id for the created subscription or false
   */
  public static function store_subscription($endpoint, $key_auth, $key_p256dh, $remote_ip) {
    global $wpdb;

    $uuid = Uuid::uuid4() -> toString();
    $result = $wpdb->insert(Perfecty_Push_Lib_Db::subscriptions_table(), [
      'uuid' => $uuid,
      'endpoint' => $endpoint,
      'key_auth' => $key_auth,
      'key_p256dh' => $key_p256dh,
      'remote_ip' => $remote_ip
    ]);

    if ($result === false) {
        error_log("Could not create the subscription: $uuid");
        return false;
    }

    $inserted_id = $wpdb->insert_id;
    return $inserted_id;
  }

  /**
   * Return the current total subscriptions
   * 
   * @return int Total subscriptions
   */
  public static function total_subscriptions() {
    global $wpdb;

    $total = $wpdb->get_var( "SELECT COUNT(*) FROM " . self::subscriptions_table() );
    return $total != null ? $total : 0;
  }

  /**
   * Changes the is_active property for the subcription
   * 
   * @param $subscription_id string id
   * @param $is_active bool True or false
   * 
   * @return int|bool Number of rows updated or false
   */
  public static function set_subscription_active($subscription_id, $is_active) {
    global $wpdb;

    $result = $wpdb->update(
      self::subscriptions_table(),
      ["is_active" => $is_active],
      ["id" => $subscription_id]
    );

    return $result;
  }

  /**
   * Get the subscription by id
   * 
   * @param $subscription_id int Subscription id
   * @return object|null Subscription or null
   */
  public static function get_subscription($subscription_id) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT " . self::$allowed_subscriptions_fields .
      " FROM " . self::subscriptions_table() . " WHERE id=%d",
      $subscription_id);
    $result = $wpdb->get_row($sql);
    return $result;
  }

  /**
   * Get the subscription by uuid
   * 
   * @param $uuid string Subscription uuid
   * @return object|null Subscription or null
   */
  public static function get_subscription_by_uuid($uuid) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT " . self::$allowed_subscriptions_fields .
      " FROM " . self::subscriptions_table() . " WHERE uuid=%d",
      $uuid);
    $result = $wpdb->get_row($sql);
    return $result;
  }

  /**
   * Get the subscriptions
   * 
   * @param $offset int Offset
   * @param $size int Limit
   * @return array The result with the subscriptions
   */
  public static function get_subscriptions($offset, $size) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT " . self::$allowed_subscriptions_fields .
      " FROM " . self::subscriptions_table() .
      " LIMIT %d OFFSET %d", $size, $offset);
    $results = $wpdb->get_results($sql);
    return $results;
  }

  /**
   * Create a notification in the DB
   * 
   * @param $payload
   * @param $status string one of the NOTIFICATIONS_STATUS_* values
   * @param $total int Total subscriptions
   * @param $batch_size int Batch size
   * 
   * @return $inserted_id or false if error
   */
  public static function create_notification($payload, $status = self::NOTIFICATIONS_STATUS_SCHEDULED, $total = 0, $batch_size= 30) {
    global $wpdb;

    $result = $wpdb->insert(Perfecty_Push_Lib_Db::notifications_table(), [
      'payload' => $payload,
      'status' => $status,
      'total' => $total,
      'batch_size' => $batch_size
    ]);

    if ($result === false) {
        error_log("Could not create the notification: " . print_r($payload, true));
        return $result;
    }

    $inserted_id = $wpdb->insert_id;
    return $inserted_id;
  }

  /**
   * Get the notification by id
   * 
   * @param $notification_id int Notification id
   * @return object|null Notification or null
   */
  public static function get_notification($notification_id) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT " . self::$allowed_notifications_fields .
      " FROM " . self::notifications_table() . " WHERE id=%d",
      $notification_id);
    $result = $wpdb->get_row($sql);
    return $result;
  }

  /**
   * Take the notification
   * 
   * @param $notification_id int Notification id
   * @return int|bool Number of rows updated or false
   */
  public static function take_notification($notification_id) {
    return self::take_untake_notification($notification_id, 1);
  }

  /**
   * Untake the notification
   * 
   * @param $notification_id int Notification id
   * @return int|bool Number of rows updated or false
   */
  public static function untake_notification($notification_id) {
    return self::take_untake_notification($notification_id, 0);
  }

  /**
   * Update the notification
   * 
   * @param $notification object Notification object
   * @return int|bool Number of rows updated or false
   */
  public static function update_notification($notification) {
    global $wpdb;

    $result = $wpdb->update(
      self::notifications_table(),
      [
        "payload" => $notification->payload,
        "total" => $notification->total,
        "succeeded" => $notification->succeeded,
        "last_cursor" => $notification->last_cursor,
        "batch_size" => $notification->batch_size,
        "status" => $notification->status,
        "is_taken" => $notification->is_taken
      ],
      ["id" => $notification->id]
    );

    return $result;
  }

  /**
   * Mark the notification as failed
   * 
   * @param $notification_id int Notification id
   * @return int|bool Number of rows updated or false
   */
  public static function mark_notification_failed($notification_id) {
    return self::mark_notification($notification_id, self::NOTIFICATIONS_STATUS_FAILED);
  }

  /**
   * Mark the notification as completed
   * 
   * @param $notification_id int Notification id
   * @return int|bool Number of rows updated or false
   */
  public static function mark_notification_completed($notification_id) {
    return self::mark_notification($notification_id, self::NOTIFICATIONS_STATUS_COMPLETED);
  }

  /**
   * Complete the notification and untake it
   * 
   * @param $notification_id int Notification id
   * @return int|bool Number of rows updated or false
   */
  public static function mark_notification_completed_untake($notification_id) {
    global $wpdb;

    $result = $wpdb->update(
      self::notifications_table(),
      [
        "status" => self::NOTIFICATIONS_STATUS_COMPLETED,
        "is_taken" => 0
      ],
      ["id" => $notification_id]
    );

    return $result;
  }

  /*****************************************************************/
  /* Private */
  /*****************************************************************/

  /**
   * Take or untake the notification
   * 
   * @param $notification_id int Notification id
   * @param $take int 0 for false, otherwise true
   * @return int|bool Number of rows updated or false
   */
  private static function take_untake_notification($notification_id, $take) {
    global $wpdb;

    $result = $wpdb->update(
      self::notifications_table(),
      ["is_taken" => $take],
      ["id" => $notification_id]
    );

    return $result;
  }

  /**
   * Mark the notification as the specified status
   * 
   * @param $notification_id int Notification id
   * @param $status string one of the NOTIFICATION_STATUS_*
   * @return int|bool Number of rows updated or false
   */
  private static function mark_notification($notification_id, $status) {
    global $wpdb;

    $result = $wpdb->update(
      self::notifications_table(),
      ["status" => $status],
      ["id" => $notification_id]
    );

    return $result;
  }
}