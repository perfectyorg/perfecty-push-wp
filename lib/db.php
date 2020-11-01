<?php

/***
 * DB library
 */
class Perfecty_Push_Lib_Db {
  
  private static $allowed_fields = "";

  private static function subscriptions_table() {
    global $wpdb;
    return $wpdb->prefix . 'perfecty_push_subscriptions';
  }

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
          endpoint VARCHAR(200) NOT NULL UNIQUE,
          key_auth VARCHAR(100) NOT NULL UNIQUE,
          key_p256dh VARCHAR(100) NOT NULL UNIQUE,
          creation_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id)
        ) $charset;";
    dbDelta( $sql );

    add_option( 'perfecty_push_version', $perfecty_push_db_version);
  }

  public function store_subscription($endpoint, $key_auth, $key_p256dh, $remote_ip) {
    global $wpdb;

    return $wpdb->insert(Perfecty_Push_Lib_Db::subscriptions_table(), [
      'endpoint' => $endpoint,
      'key_auth' => $key_auth,
      'key_p256dh' => $key_p256dh,
      'remote_ip' => $remote_ip
    ]);
  }
}