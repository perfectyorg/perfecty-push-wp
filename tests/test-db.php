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

	public function setUp()
	{
		parent::setUp();
		activate_perfecty_push();
	}

	public function tearDown()
	{
		deactivate_perfecty_push();
		parent::tearDown();
	}

	/**
	 * Test the creation of users
	 */
	public function test_user_creation() {
    global $wpdb;

		Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
    $sql = "SELECT * FROM " . $wpdb->prefix . "perfecty_push_subscriptions";
		$result = $wpdb->get_row($sql, ARRAY_A);

		$expected = [
			'id' => 1,
			'endpoint' => 'my_endpoint_url',
			'key_auth' => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip' => '127.0.0.1'
		];
		$this->assertNotEmpty($result);
		$this->assertArraySubset($expected, $result);
	}

	/**
	 * Test that newly created users are active by default
	 */
	public function test_new_users_are_active() {
    global $wpdb;

		Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
    $sql = "SELECT * FROM " . $wpdb->prefix . "perfecty_push_subscriptions";
		$result = $wpdb->get_row($sql);

		$this->assertNotEmpty($result);
		$this->assertSame('1', $result->is_active);
		$this->assertSame('0', $result->disabled);
	}

	/**
	 * Test that the store_subscription returns a valid uuid
	 */
	public function test_uuid_is_valid() {
		$id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$subscription = Perfecty_Push_Lib_Db::get_subscription($id);

		$valid = Uuid::isValid($subscription->uuid);
		$this->assertSame(true, $valid);
	}

	/**
	 * Test that the store_subscription returns false on errors
	 */
	public function test_user_creation_error_returns_false() {
		$res = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "this_is_a_really_long_string_with_more_than_46_characters");
		$this->assertSame(false, $res);
	}

	/**
	 * Test total subscriptions
	 */
	public function test_total_subscriptions() {
		$initial = Perfecty_Push_Lib_Db::total_subscriptions();
		Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$current = Perfecty_Push_Lib_Db::total_subscriptions();
		$this->assertSame($current - $initial, 1);
	}

	/**
	 * Test set subscription as active
	 */
	public function test_set_subscription_active() {
		$id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		Perfecty_Push_Lib_Db::set_subscription_active($id, false);
		$subscriptionInactive = Perfecty_Push_Lib_Db::get_subscription($id);
		Perfecty_Push_Lib_Db::set_subscription_active($id, true);
		$subscriptionActive = Perfecty_Push_Lib_Db::get_subscription($id);

		//TODO: AssertSame with booleans instead!
		$this->assertEquals(0, $subscriptionInactive->is_active);
		$this->assertEquals(1, $subscriptionActive->is_active);
	}

	/**
	 * Test get subscription by id
	 */
	public function test_get_subscription() {
		$id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$expected = [
			'endpoint' => 'my_endpoint_url',
			'key_auth' => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip' => '127.0.0.1'
		];

		$subscription = Perfecty_Push_Lib_Db::get_subscription($id);
		$this->assertArraySubset($expected, (array)$subscription);
	}

	/**
	 * Test get subscriptions
	 */
	public function test_get_subscriptions() {
		$id1 = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$id2 = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url_2", "my_key_auth_2", "my_p256dh_key_2", "127.0.0.1_2");
		$expected1 = [
			'endpoint' => 'my_endpoint_url',
			'key_auth' => 'my_key_auth',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip' => '127.0.0.1'
		];
		$expected2 = [
			'endpoint' => 'my_endpoint_url_2',
			'key_auth' => 'my_key_auth_2',
			'key_p256dh' => 'my_p256dh_key_2',
			'remote_ip' => '127.0.0.1_2'
		];

		$subscriptions = Perfecty_Push_Lib_Db::get_subscriptions(0, 2);

		$this->assertArraySubset($expected1, (array)$subscriptions[0]);
		$this->assertArraySubset($expected2, (array)$subscriptions[1]);
	}

	/**
	 * Test get subscriptions, limit/offset
	 */
	public function test_get_subscriptions_limit_offset() {
		$id1 = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$id2 = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url_2", "my_key_auth_2", "my_p256dh_key_2", "127.0.0.1_2");
		$id3 = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url_3", "my_key_auth_3", "my_p256dh_key_3", "127.0.0.1_3");
		$id4 = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url_4", "my_key_auth_4", "my_p256dh_key_4", "127.0.0.1_4");
		$expected2 = [
			'endpoint' => 'my_endpoint_url_2',
			'key_auth' => 'my_key_auth_2',
			'key_p256dh' => 'my_p256dh_key_2',
			'remote_ip' => '127.0.0.1_2'
		];
		$expected3 = [
			'endpoint' => 'my_endpoint_url_3',
			'key_auth' => 'my_key_auth_3',
			'key_p256dh' => 'my_p256dh_key_3',
			'remote_ip' => '127.0.0.1_3'
		];

		$subscriptions = Perfecty_Push_Lib_Db::get_subscriptions(1, 2);

		$this->assertSame(2, count($subscriptions));
		$this->assertArraySubset($expected2, (array)$subscriptions[0]);
		$this->assertArraySubset($expected3, (array)$subscriptions[1]);
	}

	/**
	 * Test get subscriptions, empty
	 */
	public function test_get_subscriptions_empty() {
		$subscriptions = Perfecty_Push_Lib_Db::get_subscriptions(0, 2);
		$this->assertSame([], $subscriptions);
	}

	/** 
	 * Test create notification
	 */
	public function test_user_create_notification() {
    global $wpdb;

		Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
    $sql = "SELECT * FROM " . $wpdb->prefix . "perfecty_push_notifications";
		$result = $wpdb->get_row($sql, ARRAY_A);

		$expected = [
			'id' => 1,
			'payload' => 'my_payload',
			'total' => 35,
			'succeeded' => 0,
			'last_cursor' => 0,
			'batch_size' => 20,
			'status' => 'scheduled',
			'is_taken' => 0
		];
		$this->assertNotEmpty($result);
		$this->assertArraySubset($expected, $result);
	}

	/**
	 * Test that the create notification returns false on errors
	 */
	public function test_notification_creation_error_returns_false() {
		$res = Perfecty_Push_Lib_Db::create_notification("my_payload", "this_is_a_very_long_status", 35, 20);
		$this->assertSame(false, $res);
	}

	/**
	 * Test that it gets a notification by id
	 */
	public function test_get_notification() {
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$expected = [
			'payload' => 'my_payload',
			'total' => 35,
			'succeeded' => 0,
			'last_cursor' => 0,
			'batch_size' => 20,
			'status' => 'scheduled',
			'is_taken' => 0
		];

		$notification = Perfecty_Push_Lib_Db::get_notification($id);
		$this->assertArraySubset($expected, (array)$notification);
	}

	/**
	 * Test that it returns false when the id is incorrect
	 */
	public function test_get_notification_fake_id() {
		$fake_id = 111111117;
		Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$result = Perfecty_Push_Lib_Db::get_notification($fake_id);
		$this->assertSame(null, $result);
	}

	/**
	 * Test take notification
	 */
	public function test_take_notification() {
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);
		Perfecty_Push_Lib_Db::take_notification($id);
		$taken_notification = Perfecty_Push_Lib_Db::get_notification($id);
		$this->assertEquals(0, $notification->is_taken);
		$this->assertEquals(1, $taken_notification->is_taken);
	}

	/**
	 * Test untake notification
	 */
	public function test_untake_notification() {
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);
		Perfecty_Push_Lib_Db::take_notification($id);
		$taken_notification = Perfecty_Push_Lib_Db::get_notification($id);
		Perfecty_Push_Lib_Db::untake_notification($id);
		$untaken_notification = Perfecty_Push_Lib_Db::get_notification($id);

		$this->assertEquals(0, $notification->is_taken);
		$this->assertEquals(1, $taken_notification->is_taken);
		$this->assertEquals(0, $untaken_notification->is_taken);
	}

	/**
	 * Test update notification
	 */
	public function test_update_notification() {
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);

		$notification->payload = "updated_payload";
		$notification->total = 7;
		$notification->succeeded = 1;
		$notification->last_cursor = 12;
		$notification->batch_size = 75;
		$notification->status = Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_COMPLETED;
		$notification->is_taken = 1;

		Perfecty_Push_Lib_Db::update_notification($notification);
		$updated_notification = Perfecty_Push_Lib_Db::get_notification($id);

		$expected = [
			'payload' => 'updated_payload',
			'total' => 7,
			'succeeded' => 1,
			'last_cursor' => 12,
			'batch_size' => 75,
			'status' => 'completed',
			'is_taken' => 1
		];

		$this->assertArraySubset($expected, (array)$updated_notification);
	}

	/**
	 * Test update notification, non existing
	 */
	public function test_update_notification_error() {
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);

		$notification->id = 22222112111;
		$res = Perfecty_Push_Lib_Db::update_notification($notification);
		$this->assertSame(0, $res);
	}

	/**
	 * Test mark notification as failed
	 */
	public function test_mark_notification_as_failed(){
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);
		
		Perfecty_Push_Lib_Db::mark_notification_failed($id);
		$failed_notification = Perfecty_Push_Lib_Db::get_notification($id);
		$this->assertSame("scheduled", $notification->status);
		$this->assertSame("failed", $failed_notification->status);
	}

	/**
	 * Test mark notification as completed
	 */
	public function test_mark_notification_as_completed(){
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);

		Perfecty_Push_Lib_Db::mark_notification_completed($id);
		$completed_notification = Perfecty_Push_Lib_Db::get_notification($id);
		$this->assertSame("scheduled", $notification->status);
		$this->assertSame("completed", $completed_notification->status);
	}

	/**
	 * Test mark notification as completed un untake
	 */
	public function test_mark_notification_as_completed_untake(){
		$id = Perfecty_Push_Lib_Db::create_notification("my_payload", Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20);
		Perfecty_Push_Lib_Db::take_notification($id);
		$notification = Perfecty_Push_Lib_Db::get_notification($id);

		Perfecty_Push_Lib_Db::mark_notification_completed_untake($id);
		$completed_notification = Perfecty_Push_Lib_Db::get_notification($id);
		$this->assertSame("scheduled", $notification->status);
		$this->assertEquals(1, $notification->is_taken);
		$this->assertSame("completed", $completed_notification->status);
		$this->assertEquals(0, $completed_notification->is_taken);
	}
}
