<?php

/***
 * DB library
 */
class Perfecty_Push_Lib_Db {
  
  private static $allowed_fields = "endpoint,key_auth,key_p256dh";

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
          remote_ip VARCHAR(20) DEFAULT '',
          endpoint VARCHAR(500) NOT NULL UNIQUE,
          key_auth VARCHAR(100) NOT NULL UNIQUE,
          key_p256dh VARCHAR(100) NOT NULL UNIQUE,
          creation_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id)
        ) $charset;";
    dbDelta( $sql );

    $sql = "CREATE TABLE IF NOT EXISTS " . Perfecty_Push_Lib_Db::notifications_table() . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          payload VARCHAR(500) NOT NULL UNIQUE,
          total INT(11) DEFAULT 0 NOT NULL,
          succeeded INT(11) DEFAULT 0 NOT NULL,
          cursor INT(11) DEFAULT 0 NOT NULL,
          batch_size INT(11) DEFAULT 0 NOT NULL,
          status VARCHAR(15) DEFAULT 'scheduled' NOT NULL UNIQUE,
          taken INT(1) DEFAULT 0 NOT NULL,
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
   */
  public static function store_subscription($endpoint, $key_auth, $key_p256dh, $remote_ip) {
    global $wpdb;

    $result = $wpdb->insert(Perfecty_Push_Lib_Db::subscriptions_table(), [
      'endpoint' => $endpoint,
      'key_auth' => $key_auth,
      'key_p256dh' => $key_p256dh,
      'remote_ip' => $remote_ip
    ]);

    if ($result === false) {
        error_log('DB error [last_error:' . $wpdb->last_error . ', last_query: ' . $wpdb->last_query . ']');
    }
    return $result;
  }

  /**
   * Create a notification in the DB
   * 
   * @param $payload
   * @param $status string one of the NOTIFICATIONS_STATUS_* values
   * 
   * @return $inserted_id or false if error
   */
  public static function create_notification($payload, $status = self::NOTIFICATIONS_STATUS_COMPLETED) {
    global $wpdb;

    $result = $wpdb->insert(Perfecty_Push_Lib_Db::notifications_table(), [
      'payload' => $payload,
      'status' => $status
    ]);

    if ($result === false) {
        error_log('Could not create the notification in the DB');
        error_log('DB error [last_error:' . $wpdb->last_error . ', last_query: ' . $wpdb->last_query . ']');
        return $result;
    }

    $inserted_id = $wpdb->insert_id;
    return $inserted_id;
  }

  /**
   * Get the subscriptions
   * 
   * @return array The result with the subscriptions
   */
  public static function get_subscriptions() {
    global $wpdb;

    $sql = "SELECT " . self::$allowed_fields . " FROM " . self::subscriptions_table();
    $results = $wpdb->get_results($sql);
    return $results;
  }
}