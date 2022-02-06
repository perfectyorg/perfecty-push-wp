<?php

use Perfecty_Push_External_Uuid as Uuid;

/***
 * DB library
 */
class Perfecty_Push_Lib_Db {

	private static $allowed_users_fields         = 'id,uuid,wp_user_id,endpoint,key_auth,key_p256dh,remote_ip,created_at';
	private static $allowed_notifications_fields = 'id,payload,total,succeeded,failed,last_cursor,batch_size,status,is_taken,created_at,finished_at,last_execution_at,scheduled_at';
	private static $allowed_logs_fields          = 'level,message,created_at';

	public const NOTIFICATIONS_STATUS_SCHEDULED = 'scheduled';
	public const NOTIFICATIONS_STATUS_RUNNING   = 'running';
	public const NOTIFICATIONS_STATUS_CANCELED  = 'canceled';
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

	private static function logs_table() {
		return self::with_prefix( 'perfecty_push_logs' );
	}

	/**
	 * Creates the tables in the WordPress DB and register the DB version
	 */
	public static function db_create() {
		global $wpdb;

		$db_version = get_option( 'perfecty_push_db_version' );

		// We need this for dbDelta() to work
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset             = $wpdb->get_charset_collate();
		$user_table          = self::users_table();
		$notifications_table = self::notifications_table();
		$logs_table          = self::logs_table();

		// We execute the queries per table
		$sql = "CREATE TABLE $user_table (
          id int(11) NOT NULL AUTO_INCREMENT,
          wp_user_id int(11) NULL,
          uuid char(36) NOT NULL,
          remote_ip varchar(46) DEFAULT '',
          endpoint varchar(500) NOT NULL,
          key_auth varchar(100) NOT NULL UNIQUE,
          key_p256dh varchar(100) NOT NULL UNIQUE,
          created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY  (id),
          UNIQUE KEY users_uuid_uk (uuid)
        ) $charset;";
		dbDelta( $sql );

		$sql = "CREATE TABLE $notifications_table (
          id int(11) NOT NULL AUTO_INCREMENT,
          payload varchar(2000) NOT NULL,
          total int(11) DEFAULT 0 NOT NULL,
          succeeded int(11) DEFAULT 0 NOT NULL,
          failed int(11) DEFAULT 0 NOT NULL,
          last_cursor int(11) DEFAULT 0 NOT NULL,
          batch_size int(11) DEFAULT 0 NOT NULL,
          status varchar(15) DEFAULT 'scheduled' NOT NULL,
          is_taken tinyint(1) DEFAULT 0 NOT NULL,
          created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          last_execution_at datetime NULL,
          scheduled_at datetime NULL,
          finished_at datetime NULL,
          PRIMARY KEY  (id)
        ) $charset;";
		dbDelta( $sql );

		$sql = "CREATE TABLE $logs_table (
          level varchar(10) DEFAULT 'debug',
          message varchar(2000) NOT NULL,
          created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL
        ) $charset;";
		dbDelta( $sql );

		if ( $db_version != PERFECTY_PUSH_DB_VERSION ) {
			if ( $db_version == 1 ) {
				// manual: dbDelta doesn't drop indexes
				if ( $wpdb->get_var( "SHOW INDEX FROM $user_table WHERE Key_name='users_endpoint_uk'" ) !== null ) {
					$wpdb->query( "ALTER TABLE $user_table DROP INDEX users_endpoint_uk" );
				}
				if ( $wpdb->get_var( "SHOW INDEX FROM $user_table WHERE Key_name='endpoint'" ) !== null ) {
					$wpdb->query( "ALTER TABLE $user_table DROP INDEX endpoint" );
				}
			}
			if ( $db_version == 5 ) {
				// the counters have been adjusted, we migrate the old stats
				$wpdb->query( "UPDATE $notifications_table SET failed = total - succeeded" );
			}

			update_option( 'perfecty_push_db_version', PERFECTY_PUSH_DB_VERSION );
		}
	}

	/**
	 * Check that the database tables exist
	 *
	 * @return bool
	 */
	public static function has_tables() {
		global $wpdb;

		return $wpdb->get_var( "SHOW TABLES LIKE '" . self::users_table() . "'" ) &&
			$wpdb->get_var( "SHOW TABLES LIKE '" . self::notifications_table() . "'" );
	}

	/**
	 * Store the user in the DB
	 *
	 * @param $endpoint
	 * @param $key_auth
	 * @param $key_p256dh
	 * @param $remote_ip
	 * @param $wp_user_id
	 *
	 * @return $uuid The id for the created user or false
	 * @throws Exception
	 */
	public static function create_user( $endpoint, $key_auth, $key_p256dh, $remote_ip, $wp_user_id = null ) {
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
				'wp_user_id' => $wp_user_id,
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

		$total = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::users_table() );
		return $total != null ? intval( $total ) : 0;
	}

	/**
	 * Get the users stats. $result contains:
	 * - total
	 *
	 * @return array Result
	 */
	public static function get_users_stats() {
		global $wpdb;

		$total = intval( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::users_table() ) );

		return array(
			'total' => $total,
		);
	}

	/**
	 * Delete the user by the endpoint
	 *
	 * @param $endpoint string Endpoint (unique)
	 *
	 * @return int|bool Number of rows updated or false
	 */
	public static function delete_user_by_endpoint( $endpoint ) {
		global $wpdb;

		$sql = $wpdb->prepare( 'DELETE FROM ' . self::users_table() . ' WHERE endpoint=%s', $endpoint );
		return $wpdb->query( $sql );
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

		if ( ! $uuid ) {
			return null;
		}

		$sql    = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() . ' WHERE uuid=%s',
			$uuid
		);
		$result = $wpdb->get_row( $sql );
		return $result;
	}

	/**
	 * Get the user by the specified fields
	 *
	 * @param $uuid string User uuid
	 * @param $key_auth string Key Auth
	 * @param $key_p256dh string Key P256DH
	 * @return object|null User or null
	 */
	public static function get_user_by( $uuid, $key_auth, $key_p256dh ) {
		global $wpdb;

		$sql    = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() . ' WHERE uuid=%s or (key_auth=%s && key_p256dh=%s)',
			$uuid,
			$key_auth,
			$key_p256dh
		);
		$result = $wpdb->get_row( $sql );
		return $result;
	}

	/**
	 * Update the user
	 *
	 * @param $user object User object
	 * @return int|bool Number of rows updated or false on error
	 */
	public static function update_user( $user ) {
		global $wpdb;

		$result = $wpdb->update(
			self::users_table(),
			array(
				'remote_ip'  => $user->remote_ip,
				'endpoint'   => $user->endpoint,
				'key_auth'   => $user->key_auth,
				'key_p256dh' => $user->key_p256dh,
				'wp_user_id' => $user->wp_user_id,
			),
			array( 'id' => $user->id )
		);

		return $result;
	}

	/**
	 * Delete user by id
	 *
	 * @param $user array User ids
	 * @return int|null Number of affected rows or null
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
	public static function get_users( $offset, $size, $order_by = 'created_at', $order_asc = 'desc', $mode = OBJECT ) {
		global $wpdb;

		if ( strpos( self::$allowed_users_fields, $order_by ) === false ) {
			throw new Exception( "The order by [$order_by] field is not allowed" );
		}
		$order_asc = $order_asc === 'asc' ? 'asc' : 'desc';

		$sql     = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() .
			' ORDER BY ' . $order_by . ' ' . $order_asc .
			' LIMIT %d OFFSET %d',
			$size,
			$offset
		);
		$results = $wpdb->get_results( $sql, $mode );
		return $results;
	}

	/**
	 * Get the users that belong to the given WordPress User Id
	 *
	 * @param $wp_user_id int WordPress User Id
	 * @param  $mode int WPDB Mode
	 * @return array The result with the users
	 */
	public static function get_users_by_wp_user_id( $wp_user_id, $mode = OBJECT ) {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT ' . self::$allowed_users_fields .
			' FROM ' . self::users_table() .
			' WHERE wp_user_id = %d',
			$wp_user_id
		);
		return $wpdb->get_results( $sql, $mode );
	}

	/**
	 * Create a notification in the DB
	 *
	 * @param $payload
	 * @param $status string one of the NOTIFICATIONS_STATUS_* values
	 * @param $total int Total users
	 * @param $batch_size int Batch size
	 * @param $scheduled_at DateTime Scheduled at
	 *
	 * @return $inserted_id or false if error
	 */
	public static function create_notification( $payload, $status = self::NOTIFICATIONS_STATUS_SCHEDULED, $total = 0, $batch_size = Perfecty_Push_Lib_Push_Server::DEFAULT_BATCH_SIZE, $scheduled_at = null ) {
		global $wpdb;

		$scheduled_at = $scheduled_at == null ? null : date( 'Y-m-d H:i:s', $scheduled_at );
		$result       = $wpdb->insert(
			self::notifications_table(),
			array(
				'payload'      => $payload,
				'status'       => $status,
				'total'        => $total,
				'batch_size'   => $batch_size,
				'scheduled_at' => $scheduled_at,
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
	 * Get notifications that are stalled:
	 * - Status = "Running"
	 * - Not taken
	 * - Last execution > 30 seconds
	 *
	 * @return array The result
	 */
	public static function get_notifications_stalled() {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT ' . self::$allowed_notifications_fields .
			' FROM ' . self::notifications_table() .
			' WHERE (status = %s AND is_taken = %d AND last_execution_at <= NOW() - INTERVAL 30 SECOND)' .
			' OR (status = %s AND is_taken = %d AND scheduled_at <= NOW() - INTERVAL 30 SECOND)',
			self::NOTIFICATIONS_STATUS_RUNNING,
			0,
			self::NOTIFICATIONS_STATUS_SCHEDULED,
			0
		);
		return $wpdb->get_results( $sql );
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
	public static function get_notifications( $offset, $size, $order_by = 'created_at', $order_asc = 'desc', $mode = OBJECT ) {
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
	 * Get the notification stats. $result contains:
	 * - total
	 * - succeeded
	 * - failed
	 *
	 * @return array Result
	 */
	public static function get_notifications_stats() {
		global $wpdb;

		$total     = intval( $wpdb->get_var( 'SELECT SUM(total) FROM ' . self::notifications_table() . " WHERE status !='" . self::NOTIFICATIONS_STATUS_SCHEDULED . "'" ) );
		$succeeded = intval( $wpdb->get_var( 'SELECT SUM(succeeded) FROM ' . self::notifications_table() . " WHERE status !='" . self::NOTIFICATIONS_STATUS_SCHEDULED . "'" ) );
		$failed    = intval( $wpdb->get_var( 'SELECT SUM(failed) FROM ' . self::notifications_table() . " WHERE status !='" . self::NOTIFICATIONS_STATUS_SCHEDULED . "'" ) );

		return array(
			'total'     => $total,
			'succeeded' => $succeeded,
			'failed'    => $failed,
		);
	}

	/**
	 * Get the jobs stats. $result contains:
	 * - scheduled
	 * - running
	 * - failed
	 * - completed
	 *
	 * @return array Result
	 */
	public static function get_jobs_stats() {
		global $wpdb;

		$scheduled = intval( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::notifications_table() . ' WHERE status = \'' . self::NOTIFICATIONS_STATUS_SCHEDULED . '\'' ) );
		$running   = intval( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::notifications_table() . ' WHERE status = \'' . self::NOTIFICATIONS_STATUS_RUNNING . '\'' ) );
		$failed    = intval( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::notifications_table() . ' WHERE status = \'' . self::NOTIFICATIONS_STATUS_FAILED . '\'' ) );
		$completed = intval( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::notifications_table() . ' WHERE status = \'' . self::NOTIFICATIONS_STATUS_COMPLETED . '\'' ) );
		$canceled  = intval( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::notifications_table() . ' WHERE status = \'' . self::NOTIFICATIONS_STATUS_CANCELED . '\'' ) );

		return array(
			'scheduled' => $scheduled,
			'running'   => $running,
			'failed'    => $failed,
			'completed' => $completed,
			'canceled'  => $canceled,
		);
	}

	/**
	 * Get the notification daily stats. $result contains an array as:
	 * - date
	 * - succeeded (total)
	 * - failed (total)
	 *
	 * @param $start_date DateTime
	 * @param $end_date DateTime
	 * @return array Result
	 */
	public static function get_notifications_daily_stats( $start_date, $end_date ): array {
		global $wpdb;

		$table   = self::notifications_table();
		$sql     = $wpdb->prepare(
			"SELECT DATE_FORMAT(created_at, \"%%Y-%%m-%%d\") as `date`, SUM(failed) as failed, SUM(succeeded) as succeeded
                    FROM $table
                    WHERE status != %s && status != %s
                    GROUP BY `date`
                    HAVING `date` >= %s AND `date` <= %s",
			self::NOTIFICATIONS_STATUS_RUNNING,
			self::NOTIFICATIONS_STATUS_SCHEDULED,
			$start_date->format( 'Y-m-d' ),
			$end_date->format( 'Y-m-d' )
		);
		$results = $wpdb->get_results( $sql );
		if ( $results === null ) {
			return false;
		}

		// we need the int values
		$transformed = array();
		foreach ( $results as $item ) {
			$item->succeeded = intval( $item->succeeded );
			$item->failed    = intval( $item->failed );
			$transformed[]   = $item;
		}

		return $transformed;
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
				'payload'           => $notification->payload,
				'total'             => $notification->total,
				'succeeded'         => $notification->succeeded,
				'failed'            => $notification->failed,
				'last_cursor'       => $notification->last_cursor,
				'batch_size'        => $notification->batch_size,
				'status'            => $notification->status,
				'is_taken'          => $notification->is_taken,
				'last_execution_at' => $notification->last_execution_at,
				'scheduled_at'      => $notification->scheduled_at,
				'finished_at'       => $notification->finished_at,
			),
			array( 'id' => $notification->id )
		);

		return $result;
	}

	/**
	 * Mark the notification as running
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function mark_notification_running( $notification_id ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array(
				'status' => self::NOTIFICATIONS_STATUS_RUNNING,
			),
			array( 'id' => $notification_id )
		);

		return $result;
	}

	/**
	 * Mark the notification as canceled
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function mark_notification_canceled( $notification_id ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array(
				'status'      => self::NOTIFICATIONS_STATUS_CANCELED,
				'finished_at' => current_time( 'mysql', 1 ),
			),
			array( 'id' => $notification_id )
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
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array(
				'status'      => self::NOTIFICATIONS_STATUS_FAILED,
				'finished_at' => current_time( 'mysql', 1 ),
			),
			array( 'id' => $notification_id )
		);

		return $result;
	}

	/**
	 * Mark the notification as completed
	 *
	 * @param $notification_id int Notification id
	 * @return int|bool Number of rows updated or false
	 */
	public static function mark_notification_completed( $notification_id ) {
		global $wpdb;

		$result = $wpdb->update(
			self::notifications_table(),
			array(
				'status'      => self::NOTIFICATIONS_STATUS_COMPLETED,
				'finished_at' => current_time( 'mysql', 1 ),
			),
			array( 'id' => $notification_id )
		);

		return $result;
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
				'status'      => self::NOTIFICATIONS_STATUS_COMPLETED,
				'finished_at' => current_time( 'mysql', 1 ),
				'is_taken'    => 0,
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

		// First delete scheduled events.
		foreach ( $notification_ids as $nid ) {
			$args = array( intval( $nid ) );
			wp_clear_scheduled_hook( Perfecty_Push_Lib_Push_Server::BROADCAST_HOOK, $args );
		}
		return $wpdb->query( 'DELETE FROM ' . self::notifications_table() . " WHERE id IN ($ids)" );
	}

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
	 * Inserts a log entry in the DB
	 *
	 * @param string $level
	 * @param string $message
	 *
	 * @return int Inserted Id or false if error
	 */
	public static function insert_log( $level, $message ) {
		global $wpdb;

		$result = $wpdb->insert(
			self::logs_table(),
			array(
				'level'   => $level,
				'message' => $message,
			)
		);

		if ( $result === false ) {
			error_log( 'Could not insert the log: ' . $level . ' ' . $message );
			return $result;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Return the total log entries
	 *
	 * @return int Total log entries
	 */
	public static function get_total_logs() {
		global $wpdb;

		$total = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::logs_table() );
		return $total != null ? intval( $total ) : 0;
	}

	/**
	 * Get the logs
	 *
	 * @param $offset int Offset
	 * @param $size int Limit
	 * @return array The result with the logs
	 */
	public static function get_logs( $offset, $size, $order_by = 'created_at', $order_asc = 'desc', $mode = OBJECT ) {
		global $wpdb;

		if ( strpos( self::$allowed_logs_fields, $order_by ) === false ) {
			throw new Exception( "The order by [$order_by] field is not allowed" );
		}
		$order_asc = $order_asc === 'asc' ? 'asc' : 'desc';

		$sql     = $wpdb->prepare(
			'SELECT ' . self::$allowed_logs_fields .
			' FROM ' . self::logs_table() .
			' ORDER BY ' . $order_by . ' ' . $order_asc .
			' LIMIT %d OFFSET %d',
			$size,
			$offset
		);
		$results = $wpdb->get_results( $sql, $mode );
		return $results;
	}

	/**
	 * Delete the entries older than the given number of days.
	 * It has a LIMIT of 1000 to avoid impacting the DB
	 *
	 * @param $days int Number of days
	 *
	 * @return int|bool Number of rows updated or false
	 */
	public static function delete_old_logs( $days ) {
		global $wpdb;

		$sql = $wpdb->prepare( 'DELETE FROM ' . self::logs_table() . ' WHERE created_at < (NOW() - INTERVAL %d DAY) LIMIT 1000', $days );
		return $wpdb->query( $sql );
	}
}
