
<?php

use Minishlink\WebPush\WebPush;

/**
 * Class RestActiveInactiveTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Subscribers class
 */

class RestActiveInactiveTest extends WP_UnitTestCase {

	public function setUp()
	{
		parent::setUp();
    activate_perfecty_push();
		$_REQUEST['_ajax_nonce'] = wp_create_nonce('wp_rest');
	}

	public function tearDown()
	{
    \Mockery::close();
    deactivate_perfecty_push();
		unset($_REQUEST['_ajax_nonce']);
		parent::tearDown();
	}

	/**
	 * Test set user inactive
	 */
	public function test_set_inactive() {
		$id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$subscription = Perfecty_Push_Lib_Db::get_subscription($id);

		$data = [
			'is_active' => false,
			'user_id' => $subscription->uuid
		];
		$subscribers = new Perfecty_Push_Subscribers();
		$res = $subscribers->set_user_active($data);

		$updated_subscription = Perfecty_Push_Lib_Db::get_subscription($id);
		$this->assertSame((array)$res, [
			'success' => true,
			'is_active' => false
		]);

		$this->assertEquals(1, $subscription->is_active);
		$this->assertEquals(0, $updated_subscription->is_active);
	}

	/**
	 * Test set user active
	 */
	public function test_set_active() {
		$id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		Perfecty_Push_Lib_Db::set_subscription_active($id, false);
		$subscription = Perfecty_Push_Lib_Db::get_subscription($id);

		$data = [
			'is_active' => true,
			'user_id' => $subscription->uuid
		];
		$subscribers = new Perfecty_Push_Subscribers();
		$res = $subscribers->set_user_active($data);

		$updated_subscription = Perfecty_Push_Lib_Db::get_subscription($id);
		$this->assertSame((array)$res, [
			'success' => true,
			'is_active' => true
		]);

		$this->assertEquals(0, $subscription->is_active);
		$this->assertEquals(1, $updated_subscription->is_active);
	}
}