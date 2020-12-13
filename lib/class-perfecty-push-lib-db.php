<?php

use Ramsey\Uuid\Uuid;

/***
 * DB library
 */
class Perfecty_Push_Lib_Db {

	private static $allowed_users_fields         = 'id,uuid,endpoint,key_auth,key_p256dh,remote_ip,is_active,disabled,creation_time';
	private static $allowed_notifications_fields = 'id,payload,total,succeeded,last_cursor,batch_size,status,is_taken,creation_time';

	public const NOTIFICATIONS_STATUS_SCHEDULED = 'scheduled';
	public const NOTIFICATIONS_STATUS_RUNNING   = 'running';
	public const NOTIFICATIONS_STATUS_FAILED    = 'failed';
	public const NOTIFICATIONS_STATUS_COMPLETED = 'completed';

	private static function with_prefix( $table ) {
		global $wpdb;
		return $wpdb->prefix . $table;
	}

	private static function users_table() {
		return self::with_prefix( 'perfecty_push_users' );
	}

	private static function notifications_table() {
		return self::with_prefix( 'perfecty_push_notifications' );
	}

	/**
	 * Creates the tables in the WordPress DB and register the DB version
	 */
	public static function db_create() {
		global $wpdb;

		$installed_version = get_option( 'perfecty_push_db_version' );

		// We need this for dbDelta() to work
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset             = $wpdb->get_charset_collate();
		$user_table          = self::users_table();
		$notifications_table = self::notifications_table();

		// We execute the queries per table
		$sql = "CREATE TABLE IF NOT EXISTS $user_table (
          id int(11) NOT NULL AUTO_INCREMENT,
          uuid char(36) NOT NULL,
          remote_ip varchar(46) DEFAULT '',
          endpoint varchar(500) NOT NULL UNIQUE,
          key_auth varchar(100) NOT NULL UNIQUE,
          key_p256dh varchar(100) NOT NULL UNIQUE,
          is_active tinyint(1) DEFAULT 1 NOT NULL,
          disabled tinyint(1) DEFAULT 0 NOT NULL,
          creation_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id),
          UNIQUE KEY users_uuid_uk (uuid)
        ) $charset;";
		dbDelta( $sql );

		$sql = "CREATE TABLE IF NOT EXISTS $notifications_table (
          id int(11) NOT NULL AUTO_INCREMENT,
          payload varchar(500) NOT NULL,
          total int(11) DEFAULT 0 NOT NULL,
          succeeded int(11) DEFAULT 0 NOT NULL,
          last_cursor int(11) DEFAULT 0 NOT NULL,
          batch_size int(11) DEFAULT 0 NOT NULL,
          status varchar(15) DEFAULT 'scheduled' NOT NULL,
          is_taken tinyint(1) DEFAULT 0 NOT NULL,
          creation_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id)
        ) $charset;";
		dbDelta( $sql );

		if ( $installed_version != PERFECTY_PUSH_DB_VERSION ) {
			// perform update according to https://codex.wordpress.org/Creating_Tables_with_Plugins
			update_option( 'perfecty_push_db_version', PERFECTY_PUSH_DB_VERSION );
		}
	}

	/**
	 * Store the user in the DB
	 *
	 * @param $endpoint
	 * @param $key_auth
	 * @param $key_p256dh
	 * @param $remote_ip
	 *
	 * @return $uuid The id for the created user or false
	 * @throws Exception
	 */
	public static function create_user( $endpoint, $key_auth, $key_p256dh, $remote_ip ) {
		global $wpdb;

		$uuid   = Uuid::uuid4()->toString();
		$result = $wpdb->insert(
			self::users_table(),
			array(
				'uuid'       => $uuid,
				'endpoint'   => $endpoint,
				'key_auth'   => $key_auth,
				'key_p256dh' => $key_p256dh,
				'remote_ip'  => $remote_ip,
			)
		);

		if ( $result === false ) {
			error_log( "Could not create the user: $uuid" );
			return false;
		}

		$inserted_id = $wpdb->insert_id;
		return $inserted_id;
	}

	/**
	 * Return the current total users
	 *
	 * @return int Total users
	 */
	public static function get_total_users() {
		global $wpdb;

		$total = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::users_table() . ' WHERE is_active=1 and disabled=0' );
		return $total != null ? intval( $total ) : 0;
	}

	/**
	 * Changes the is_active property for the subcription
	 *
	 * @param $user_id string id
	 * @param $is_active bool True or false
	 *
	 * @return int|bool Number of rows updated or false
	 */
	public static function set_user_active( $user_id, $is_active ) {
		global $wpdb;

		return $wpdb->update(
			self::users_table(),
			array( 'is_active' => $is_active ),
			array( 'id' => $user_id )
		);
	}

	/**
	 * Changes the disabled property for the subcription
	 *
	 * @param $user_id int id
	 * @param $disabled bool True or false
	 *
	 * @return int|bool Number of rows updated or false
	 */
	public static function set_user_disabled( $user_id, $disabled ) {
		global $wpdb;

		return $wpdb->update(
			self::users_table(),
			array( 'disabled' => $disabled ),
			array( 'id' => $user_id )
		);
	}

	/**
	 * Get the user by id
	 *
	 * @param $user_id int User id
	 * @return object|null User or null
	 */
	public static function get_user( $user_id ) {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() . ' WHERE id=%d',
			$user_id
		);
		return $wpdb->get_row( $sql );
	}

	/**
	 * Get the user by uuid
	 *
	 * @param $uuid string User uuid
	 * @return object|null User or null
	 */
	public static function get_user_by_uuid( $uuid ) {
		global $wpdb;

		$sql    = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() . ' WHERE uuid=%s',
			$uuid
		);
		$result = $wpdb->get_row( $sql );
		return $result;
	}

	/**
	 * Delete user by id
	 *
	 * @param $user array User ids
	 * @return int|bool Number of affected rows or null
	 */
	public static function delete_users( $user_ids ) {
		global $wpdb;

		if ( ! is_array( $user_ids ) ) {
			error_log( 'Wrong parameter, user ids must be an array' );
			return false;
		}
		$ids = implode( ',', $user_ids );

		return $wpdb->query( 'DELETE FROM ' . self::users_table() . " WHERE id IN ($ids)" );
	}

	/**
	 * Get the users
	 *
	 * @param $offset int Offset
	 * @param $size int Limit
	 * @return array The result with the users
	 */
	public static function get_users( $offset, $size, $order_by = 'creation_time', $order_asc = 'desc', $only_active = false, $mode = OBJECT ) {
		global $wpdb;

		if ( strpos( self::$allowed_users_fields, $order_by ) === false ) {
			throw new Exception( "The order by [$order_by] field is not alllowed" );
		}
		$order_asc        = $order_asc === 'asc' ? 'asc' : 'desc';
		$where_conditions = $only_active === false ? '' : ' WHERE is_active=1 AND disabled=false';

		$sql     = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() .
			$where_conditions .
			' ORDER BY ' . $order_by . ' ' . $order_asc .
			' LIMIT %d OFFSET %d',
			$size,
			$offset
		);
		$results = $wpdb->get_results( $sql, $mode );
		return $results;
	}

	/**
	 * Create a notification in the DB
	 *
	 * @param $payload
	 * @param $status string one of the NOTIFICATIONS_STATUS_* values
	 * @param $total int Total users
	 * @param $batch_size int Batch size
	 *
	 * @return $inserted_id or false if error
	 */
	public static function create_notification( $payload, $status = self::NOTIFICATIONS_STATUS_SCHEDULED, $total = 0, $batch_size = 30 ) {
		global $wpdb;

		$result = $wpdb->insert(
			self::notifications_table(),
			array(
				'payload'    => $payload,
				'status'     => $status,
				'total'      => $total,
				'batch_size' => $batch_size,
			)
		);

		if ( $result === false ) {
			error_log( 'Could not create the notification: ' . print_r( $payload, true ) );
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
	public static function get_notification( $notification_id ) {
		global $wpdb;

		$sql    = $wpdb->prepare(
			'SELECT ' . self::$allowed_notifications_fields .
			' FROM ' . self::notifications_table() . ' WHERE id=%d',
			$notification_id
		);
		$result = $wpdb->get_row( $sql );
		return $result;
	}

	/**
	 * Get notifications
	 *
	 * @param $offset int Offset
	 * @param $size int Limit
	 * @param $order_by string Field to order by
	 * @param $order_asc string 'asc' or 'desc'
	 * @param $mode int How to return the results
	 * @return array The result
	 */
	public static function get_notifications( $offset, $size, $order_by = 'creation_time', $order_asc = 'desc', $mode = OBJECT ) {
		global $wpdb;

		if ( strpos( self::$allowed_notifications_fields, $order_by ) === false ) {
			throw new Exception( "The order by [$order_by] field is not alllowed" );
		}
		$order_asc = $order_asc === 'asc' ? 'asc' : 'desc';

		$sql     = $wpdb->prepare(
			'SELECT ' . self::$allowed_notifications_fields .
			' FROM ' . self::notifications_table() .
			' ORDER BY ' . $order_by . ' ' . $order_asc .
			' LIMIT %d OFFSET %d',
			$size,
			$offset
		);
		$results = $wpdb->get_results( $sql, $mode );
		return $results;
	}

	/**
	 * Get total notifications
	 *
	 * @return int Total notifications
	 */
	public static function get_notifications_total() {
		global $wpdb;

		$total = $wpdb->get_var( 'SELECT COUNT(id) FROM ' . self::notifications_table() );
		return intval( $total );
	}

	/**
	 * Take the notification
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function take_notification( $notification_id ) {
		return self::take_untake_notification( $notification_id, 1 );
	}

	/**
	 * Untake the notification
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function untake_notification( $notification_id ) {
		return self::take_untake_notification( $notification_id, 0 );
	}

	/**
	 * Update the notification
	 *
	 * @param $notification object Notification object
	 * @return int|bool Number of rows updated or false
	 */
	public static function update_notification( $notification ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array(
				'payload'     => $notification->payload,
				'total'       => $notification->total,
				'succeeded'   => $notification->succeeded,
				'last_cursor' => $notification->last_cursor,
				'batch_size'  => $notification->batch_size,
				'status'      => $notification->status,
				'is_taken'    => $notification->is_taken,
			),
			array( 'id' => $notification->id )
		);

		return $result;
	}

	/**
	 * Mark the notification as failed
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function mark_notification_failed( $notification_id ) {
		return self::mark_notification( $notification_id, self::NOTIFICATIONS_STATUS_FAILED );
	}

	/**
	 * Mark the notification as completed
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function mark_notification_completed( $notification_id ) {
		return self::mark_notification( $notification_id, self::NOTIFICATIONS_STATUS_COMPLETED );
	}

	/**
	 * Complete the notification and untake it
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function mark_notification_completed_untake( $notification_id ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array(
				'status'   => self::NOTIFICATIONS_STATUS_COMPLETED,
				'is_taken' => 0,
			),
			array( 'id' => $notification_id )
		);

		return $result;
	}

	/**
	 * Delete notifications by id
	 *
	 * @param $notification_ids array Notification ids
	 * @return int|bool Number of affected rows or null
	 */
	public static function delete_notifications( $notification_ids ) {
		global $wpdb;

		if ( ! is_array( $notification_ids ) ) {
			error_log( 'Wrong parameter, notification ids must be an array' );
			return false;
		}
		$ids = implode( ',', $notification_ids );

		return $wpdb->query( 'DELETE FROM ' . self::notifications_table() . " WHERE id IN ($ids)" );
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
	private static function take_untake_notification( $notification_id, $take ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array( 'is_taken' => $take ),
			array( 'id' => $notification_id )
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
	private static function mark_notification( $notification_id, $status ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array( 'status' => $status ),
			array( 'id' => $notification_id )
		);

		return $result;
	}
}
