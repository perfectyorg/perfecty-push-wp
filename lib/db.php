<?php

/***
 * DB library
 */
class Perfecty_Push_Lib_Db {
  
  private static $allowed_fields = "endpoint,key_auth,key_p256dh";

  private static function subscriptions_table() {
    global $wpdb;
    return $wpdb->prefix . 'perfecty_push_subscriptions';
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