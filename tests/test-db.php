<?php
/**
 * Class DbTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Lib_Db
 */

use Ramsey\Uuid\Uuid;

class DbTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		activate_perfecty_push();
	}

	public function tearDown() {
		deactivate_perfecty_push();
		parent::tearDown();
	}

	/**
	 * Test the creation of users
	 */
	public function test_user_creation() {
		global $wpdb;

		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$sql    = 'SELECT * FROM ' . $wpdb->prefix . 'perfecty_push_users';
		$result = $wpdb->get_row( $sql, ARRAY_A );

		$expected = array(
			'id'         => 1,
			'endpoint'   => 'my_endpoint_url',
			'key_auth'   => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip'  => '127.0.0.1',
		);
		$this->assertNotEmpty( $result );
		$this->assertArraySubset( $expected, $result );
	}

	/**
	 * Test that newly created users are active by default
	 */
	public function test_new_users_are_active() {
		global $wpdb;

		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$sql    = 'SELECT * FROM ' . $wpdb->prefix . 'perfecty_push_users';
		$result = $wpdb->get_row( $sql );

		$this->assertNotEmpty( $result );
		$this->assertSame( '1', $result->is_active );
		$this->assertSame( '0', $result->disabled );
	}

	/**
	 * Test that the create_user returns a valid uuid
	 */
	public function test_uuid_is_valid() {
		$id           = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$user = Perfecty_Push_Lib_Db::get_user( $id );

		$valid = Uuid::isValid( $user->uuid );
		$this->assertSame( true, $valid );
	}

	/**
	 * Test that the create_user returns false on errors
	 */
	public function test_user_creation_error_returns_false() {
		$res = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', 'this_is_a_really_long_string_with_more_than_46_characters' );
		$this->assertSame( false, $res );
	}

	/**
	 * Test total users
	 */
	public function test_get_total_users() {
		$initial = Perfecty_Push_Lib_Db::get_total_users();
		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$current = Perfecty_Push_Lib_Db::get_total_users();
		$this->assertSame( $current - $initial, 1 );
	}

    /**
     * Test the users stats
     */
    public function test_get_users_stats() {
        $id1           = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_1', 'my_key_auth_1', 'my_p256dh_key_1', '127.0.0.1' );
        $id2           = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_2', 'my_key_auth_2', 'my_p256dh_key_2', '127.0.0.1' );
        $id3           = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_3', 'my_key_auth_3', 'my_p256dh_key_3', '127.0.0.1' );

        Perfecty_Push_Lib_Db::set_user_active($id1, false);
        Perfecty_Push_Lib_Db::set_user_active($id2, false);

        $result = Perfecty_Push_Lib_Db::get_users_stats();

        $this->assertSame(3, $result['total']);
        $this->assertSame(1, $result['active']);
        $this->assertSame(2, $result['inactive']);
    }

	/**
	 * Test set user as disabled
	 */
	public function test_set_user_disabled() {
		$id = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::set_user_disabled( $id, true);
		$userDisabled = Perfecty_Push_Lib_Db::get_user( $id );
		Perfecty_Push_Lib_Db::set_user_disabled( $id, false );
		$userEnabled = Perfecty_Push_Lib_Db::get_user( $id );

		// TODO: AssertSame with booleans instead!
		$this->assertEquals( 1, $userDisabled->disabled);
		$this->assertEquals( 0, $userEnabled->disabled);
	}

	/**
	 * Test set user as active
	 */
	public function test_set_user_active() {
		$id = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::set_user_active( $id, false );
		$userInactive = Perfecty_Push_Lib_Db::get_user( $id );
		Perfecty_Push_Lib_Db::set_user_active( $id, true );
		$userActive = Perfecty_Push_Lib_Db::get_user( $id );

		// TODO: AssertSame with booleans instead!
		$this->assertEquals( 0, $userInactive->is_active );
		$this->assertEquals( 1, $userActive->is_active );
	}

	/**
	 * Test get user by id
	 */
	public function test_get_user() {
		$id       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$expected = array(
			'endpoint'   => 'my_endpoint_url',
			'key_auth'   => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip'  => '127.0.0.1',
		);

		$user = Perfecty_Push_Lib_Db::get_user( $id );
		$this->assertArraySubset( $expected, (array) $user );
	}

	/**
	 * Test get user by uuid
	 */
	public function test_get_user_by_uuid() {
		$id              = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$expected        = array(
			'endpoint'   => 'my_endpoint_url',
			'key_auth'   => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip'  => '127.0.0.1',
		);
		$user_db = Perfecty_Push_Lib_Db::get_user( $id );

		$user = Perfecty_Push_Lib_Db::get_user_by_uuid( $user_db->uuid );
		$this->assertArraySubset( $expected, (array) $user );
	}
	/**
	 * Test get users
	 */
	public function test_get_users() {
		$id1       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$id2       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_2', 'my_key_auth_2', 'my_p256dh_key_2', '127.0.0.1_2' );
		$expected1 = array(
			'endpoint'   => 'my_endpoint_url',
			'key_auth'   => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip'  => '127.0.0.1',
		);
		$expected2 = array(
			'endpoint'   => 'my_endpoint_url_2',
			'key_auth'   => 'my_key_auth_2',
			'key_p256dh' => 'my_p256dh_key_2',
			'remote_ip'  => '127.0.0.1_2',
		);

		$users = Perfecty_Push_Lib_Db::get_users( 0, 2, 'id', 'desc' );

		$this->assertArraySubset( $expected1, (array) $users[1] );
		$this->assertArraySubset( $expected2, (array) $users[0] );
	}

	/**
	 * Test get users, limit/offset
	 */
	public function test_get_users_limit_offset() {
		$id1       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$id2       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_2', 'my_key_auth_2', 'my_p256dh_key_2', '127.0.0.1_2' );
		$id3       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_3', 'my_key_auth_3', 'my_p256dh_key_3', '127.0.0.1_3' );
		$id4       = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_4', 'my_key_auth_4', 'my_p256dh_key_4', '127.0.0.1_4' );
		$expected2 = array(
			'endpoint'   => 'my_endpoint_url_2',
			'key_auth'   => 'my_key_auth_2',
			'key_p256dh' => 'my_p256dh_key_2',
			'remote_ip'  => '127.0.0.1_2',
		);
		$expected3 = array(
			'endpoint'   => 'my_endpoint_url_3',
			'key_auth'   => 'my_key_auth_3',
			'key_p256dh' => 'my_p256dh_key_3',
			'remote_ip'  => '127.0.0.1_3',
		);

		$users = Perfecty_Push_Lib_Db::get_users( 1, 2 );

		$this->assertSame( 2, count( $users ) );
		$this->assertArraySubset( $expected2, (array) $users[0] );
		$this->assertArraySubset( $expected3, (array) $users[1] );
	}

	/**
	 * Test get users, empty
	 */
	public function test_get_users_empty() {
		$users = Perfecty_Push_Lib_Db::get_users( 0, 2 );
		$this->assertSame( array(), $users );
	}

	/**
	 * Test delete users
	 */
	public function test_delete_users() {
		$id1          = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$id2          = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url_2', 'my_key_auth_2', 'my_p256dh_key_2', '127.0.0.1_2' );
		$users_before = Perfecty_Push_Lib_Db::get_users(0, 30);

		$total = Perfecty_Push_Lib_Db::delete_users([$id1, $id2]);
		$users_after = Perfecty_Push_Lib_Db::get_users(0, 30);

		$this->assertSame($total, 2);
		$this->assertSame(count($users_before), 2);
		$this->assertSame(count($users_after), 0);
	}

	/**
	 * Test create notification
	 */
	public function test_user_create_notification() {
		global $wpdb;

		Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$sql    = 'SELECT * FROM ' . $wpdb->prefix . 'perfecty_push_notifications';
		$result = $wpdb->get_row( $sql, ARRAY_A );

		$expected = array(
			'id'          => 1,
			'payload'     => 'my_payload',
			'total'       => 35,
			'succeeded'   => 0,
			'last_cursor' => 0,
			'batch_size'  => 20,
			'status'      => 'scheduled',
			'is_taken'    => 0,
		);
		$this->assertNotEmpty( $result );
		$this->assertArraySubset( $expected, $result );
	}

	/**
	 * Test delete notifications
	 */
	public function test_delete_notification() {
		$id1 = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$id2 = Perfecty_Push_Lib_Db::create_notification( 'my_payload_2', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notifications_before = Perfecty_Push_Lib_Db::get_notifications(0, 30);

		$total = Perfecty_Push_Lib_Db::delete_notifications([$id1, $id2]);
		$notifications_after = Perfecty_Push_Lib_Db::get_notifications(0, 30);

		$this->assertSame($total, 2);
		$this->assertSame(count($notifications_before), 2);
		$this->assertSame(count($notifications_after), 0);
	}

	/**
	 * Test that the create notification returns false on errors
	 */
	public function test_notification_creation_error_returns_false() {
		$res = Perfecty_Push_Lib_Db::create_notification( 'my_payload', 'this_is_a_very_long_status', 35, 20 );
		$this->assertSame( false, $res );
	}

	/**
	 * Test that it gets a notification by id
	 */
	public function test_get_notification() {
		$id       = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$expected = array(
			'payload'     => 'my_payload',
			'total'       => 35,
			'succeeded'   => 0,
			'last_cursor' => 0,
			'batch_size'  => 20,
			'status'      => 'scheduled',
			'is_taken'    => 0,
		);

		$notification = Perfecty_Push_Lib_Db::get_notification( $id );
		$this->assertArraySubset( $expected, (array) $notification );
	}

	/**
	 * Test that it gets a notifications
	 */
	public function test_get_notifications() {
		$id1       = Perfecty_Push_Lib_Db::create_notification( 'my_payload1', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$id2       = Perfecty_Push_Lib_Db::create_notification( 'my_payload2', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_COMPLETED, 43, 17 );
		$id3       = Perfecty_Push_Lib_Db::create_notification( 'my_payload3', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_FAILED, 55, 88 );
		$expected1 = array(
			'payload'     => 'my_payload1',
			'total'       => 35,
			'succeeded'   => 0,
			'last_cursor' => 0,
			'batch_size'  => 20,
			'status'      => 'scheduled',
			'is_taken'    => 0,
		);

		$expected2 = array(
			'payload'     => 'my_payload2',
			'total'       => 43,
			'succeeded'   => 0,
			'last_cursor' => 0,
			'batch_size'  => 17,
			'status'      => 'completed',
			'is_taken'    => 0,
		);

		$expected3 = array(
			'payload'     => 'my_payload3',
			'total'       => 55,
			'succeeded'   => 0,
			'last_cursor' => 0,
			'batch_size'  => 88,
			'status'      => 'failed',
			'is_taken'    => 0,
		);
		$notifications = Perfecty_Push_Lib_Db::get_notifications(0, 5, 'id', 'desc');
		$this->assertSame( 3, count($notifications) );
		$this->assertArraySubset( $expected1, (array) $notifications[2] );
		$this->assertArraySubset( $expected2, (array) $notifications[1] );
		$this->assertArraySubset( $expected3, (array) $notifications[0] );
	}

	/**
	 * Test the total notifications
	 */
	public function test_total_notifications() {
		$id1       = Perfecty_Push_Lib_Db::create_notification( 'my_payload1', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$id2       = Perfecty_Push_Lib_Db::create_notification( 'my_payload2', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 43, 17 );

		$total = Perfecty_Push_Lib_Db::get_notifications_total();
		$this->assertSame(2, $total);
	}

    /**
     * Test the notifications stats
     */
    public function test_get_notifications_stats() {
        $id1       = Perfecty_Push_Lib_Db::create_notification( 'my_payload1', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
        $id2       = Perfecty_Push_Lib_Db::create_notification( 'my_payload2', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 45, 17 );
        $id3       = Perfecty_Push_Lib_Db::create_notification( 'my_payload2', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 10, 17 );

        $notification_1 = Perfecty_Push_Lib_Db::get_notification($id1);
        $notification_1->succeeded = 70;
        Perfecty_Push_Lib_Db::update_notification($notification_1);
        $notification_2 = Perfecty_Push_Lib_Db::get_notification($id2);
        $notification_2->succeeded = 7;
        Perfecty_Push_Lib_Db::update_notification($notification_2);

        $result = Perfecty_Push_Lib_Db::get_notifications_stats();

        $this->assertSame(90, $result['total']);
        $this->assertSame(77, $result['succeeded']);
        $this->assertSame(13, $result['failed']);
    }

    /**
     * Test the notifications stats
     */
    public function test_get_jobs_stats() {
        $id1       = Perfecty_Push_Lib_Db::create_notification( 'my_payload1', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
        $id2       = Perfecty_Push_Lib_Db::create_notification( 'my_payload2', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING, 45, 17 );
        $id3       = Perfecty_Push_Lib_Db::create_notification( 'my_payload3', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_COMPLETED, 10, 11 );
        $id4       = Perfecty_Push_Lib_Db::create_notification( 'my_payload4', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_FAILED, 7, 21 );
        $id5       = Perfecty_Push_Lib_Db::create_notification( 'my_payload5', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING, 27, 30 );

        $result = Perfecty_Push_Lib_Db::get_jobs_stats();

        $this->assertSame(1, $result['scheduled']);
        $this->assertSame(2, $result['running']);
        $this->assertSame(1, $result['failed']);
        $this->assertSame(1, $result['completed']);
    }

	/**
	 * Test that it returns false when the id is incorrect
	 */
	public function test_get_notification_fake_id() {
		$fake_id = 111111117;
		Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$result = Perfecty_Push_Lib_Db::get_notification( $fake_id );
		$this->assertSame( null, $result );
	}

	/**
	 * Test take notification
	 */
	public function test_take_notification() {
		$id           = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );
		Perfecty_Push_Lib_Db::take_notification( $id );
		$taken_notification = Perfecty_Push_Lib_Db::get_notification( $id );
		$this->assertEquals( 0, $notification->is_taken );
		$this->assertEquals( 1, $taken_notification->is_taken );
	}

	/**
	 * Test untake notification
	 */
	public function test_untake_notification() {
		$id           = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );
		Perfecty_Push_Lib_Db::take_notification( $id );
		$taken_notification = Perfecty_Push_Lib_Db::get_notification( $id );
		Perfecty_Push_Lib_Db::untake_notification( $id );
		$untaken_notification = Perfecty_Push_Lib_Db::get_notification( $id );

		$this->assertEquals( 0, $notification->is_taken );
		$this->assertEquals( 1, $taken_notification->is_taken );
		$this->assertEquals( 0, $untaken_notification->is_taken );
	}

	/**
	 * Test update notification
	 */
	public function test_update_notification() {
		$id           = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );

		$notification->payload     = 'updated_payload';
		$notification->total       = 7;
		$notification->succeeded   = 1;
		$notification->last_cursor = 12;
		$notification->batch_size  = 75;
		$notification->status      = Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_COMPLETED;
		$notification->is_taken    = 1;

		Perfecty_Push_Lib_Db::update_notification( $notification );
		$updated_notification = Perfecty_Push_Lib_Db::get_notification( $id );

		$expected = array(
			'payload'     => 'updated_payload',
			'total'       => 7,
			'succeeded'   => 1,
			'last_cursor' => 12,
			'batch_size'  => 75,
			'status'      => 'completed',
			'is_taken'    => 1,
		);

		$this->assertArraySubset( $expected, (array) $updated_notification );
	}

	/**
	 * Test update notification, non existing
	 */
	public function test_update_notification_error() {
		$id           = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );

		$notification->id = 22222112111;
		$res              = Perfecty_Push_Lib_Db::update_notification( $notification );
		$this->assertSame( 0, $res );
	}

	/**
	 * Test mark notification as failed
	 */
	public function test_mark_notification_as_failed() {
		$id           = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );

		Perfecty_Push_Lib_Db::mark_notification_failed( $id );
		$failed_notification = Perfecty_Push_Lib_Db::get_notification( $id );
		$this->assertSame( 'scheduled', $notification->status );
		$this->assertSame( 'failed', $failed_notification->status );
	}

	/**
	 * Test mark notification as completed
	 */
	public function test_mark_notification_as_completed() {
		$id           = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );

		Perfecty_Push_Lib_Db::mark_notification_completed( $id );
		$completed_notification = Perfecty_Push_Lib_Db::get_notification( $id );
		$this->assertSame( 'scheduled', $notification->status );
		$this->assertSame( 'completed', $completed_notification->status );
	}

	/**
	 * Test mark notification as completed un untake
	 */
	public function test_mark_notification_as_completed_untake() {
		$id = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		Perfecty_Push_Lib_Db::take_notification( $id );
		$notification = Perfecty_Push_Lib_Db::get_notification( $id );

		Perfecty_Push_Lib_Db::mark_notification_completed_untake( $id );
		$completed_notification = Perfecty_Push_Lib_Db::get_notification( $id );
		$this->assertSame( 'scheduled', $notification->status );
		$this->assertEquals( 1, $notification->is_taken );
		$this->assertSame( 'completed', $completed_notification->status );
		$this->assertEquals( 0, $completed_notification->is_taken );
	}
}
