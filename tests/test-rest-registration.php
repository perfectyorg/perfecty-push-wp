
<?php

use Minishlink\WebPush\WebPush;

/**
 * Class RestRegistrationTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Subscribers class
 */

class RestRegistrationTest extends WP_UnitTestCase {

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
	 * Test the user registration
	 */
	public function test_registration() {
		$subscribers = new Perfecty_Push_Subscribers();
		$data = [
			'subscription' => [
				'endpoint' => 'http://my_endpoint',
				'keys' => [
					'auth' => 'my_auth_key',
					'p256dh' => 'my_p256dh_key'
				]
			]
		];
		
		$res = $subscribers->register($data);
		$subscriptions = Perfecty_Push_Lib_Db::get_subscriptions(0, 5);

		$expected = [
			'endpoint' => 'http://my_endpoint',
			'key_auth' => 'my_auth_key',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip' => '127.0.0.1'
		];

		$this->assertSame(1, count($subscriptions));
		$this->assertSame((array)$res, [
			'success' => true,
			'uuid' => $subscriptions[0]->uuid
		]);
		$this->assertArraySubset($expected, (array)$subscriptions[0]);
	}

	/**
	 * Test registration invalid
	 */
	public function test_registration_invalid() {
		$subscribers = new Perfecty_Push_Subscribers();
		$data = [
			'subscription' => [
				'endpoint' => 'http://my_endpoint',
				'keys' => [
					'auth' => '',
					'p256dh' => 'my_p256dh_key'
				]
			]
		];
		
		$res = $subscribers->register($data);

		$expected = [
			"validation_error" => [
				0 => "Missing the auth key"
			]
		];

		$this->assertInstanceOf(WP_Error::class, $res);
		$this->assertArraySubset($expected, $res->errors);
		$this->assertSame(400, $res->error_data["validation_error"]["status"]);
	}

	/**
	 * Test registration missing important data
	 */
	public function test_registration_missing_data() {
		$subscribers = new Perfecty_Push_Subscribers();
		$data = [
			'subscription' => [
				'keys' => [
					'p256dh' => 'my_p256dh_key'
				]
			]
		];
		
		$res = $subscribers->register($data);

		$expected = [
			"validation_error" => [
				0 => "No endpoint was provided in the request"
			]
		];

		$this->assertInstanceOf(WP_Error::class, $res);
		$this->assertArraySubset($expected, $res->errors);
		$this->assertSame(400, $res->error_data["validation_error"]["status"]);
	}

	/**
	 * Test registration DB error
	 */
	public function test_registration_db_error() {
		$subscribers = new Perfecty_Push_Subscribers();
		$data = [
			'subscription' => [
				'endpoint' => 'http://my_endpoint',
				'keys' => [
					'auth' => 'my_very_long_auth_key123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
					'p256dh' => 'my_p256dh_key'
				]
			]
		];
		
		$res = $subscribers->register($data);

		$expected = [
			"failed_subscription" => [
				0 => "Could not subscribe the user"
			]
		];

		$this->assertInstanceOf(WP_Error::class, $res);
		$this->assertArraySubset($expected, $res->errors);
		$this->assertSame(500, $res->error_data["failed_subscription"]["status"]);
	}

	/**
	 * Test the user registration with invalid nonce
	 */
	public function test_registration_invalid_nonce() {
		unset($_REQUEST['_ajax_nonce']);
		$mock = Mockery::mock(Perfecty_Push_Subscribers::class)->makePartial();
		$mock
		->shouldReceive('terminate')
		->andReturnUsing(function(){
			throw new InvalidNonceException();
		});

		$this->expectException(InvalidNonceException::class);
		$mock->register([]);
	}
}