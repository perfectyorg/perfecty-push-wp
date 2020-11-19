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
		$this->assertEquals(1, $result->is_active);
		$this->assertEquals(0, $result->disabled);
	}

	/**
	 * Test that the store_subscription returns a valid uuid
	 */
	public function test_uuid_is_valid() {
		['uuid' => $res] = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$valid = Uuid::isValid($res);
		$this->assertEquals(true, $valid);
	}

	/**
	 * Test that the store_subscription returns false on errors
	 */
	public function test_user_creation_error_returns_false() {
		$res = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "this_is_a_really_long_string_more_than_46_characters");
		$this->assertEquals(false, $res);
	}
}
