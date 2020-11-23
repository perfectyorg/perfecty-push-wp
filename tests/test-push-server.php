<?php

use Minishlink\WebPush\WebPush;

/**
 * Class PushServerTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Lib_Push_Server class
 */

class PushServerTest extends WP_UnitTestCase {

  private $empty_callback = 'echo "mocked"';

	public function setUp()
	{
    parent::setUp();
    activate_perfecty_push();

    if (!defined('PERFECTY_PUSH_VAPID_PRIVATE_KEY') && !defined('PERFECTY_PUSH_VAPID_PUBLIC_KEY')) {
      define('PERFECTY_PUSH_VAPID_PRIVATE_KEY', 'test_private_key');
      define('PERFECTY_PUSH_VAPID_PUBLIC_KEY', 'test_public_key');
    }
	}

	public function tearDown()
	{
		$webpush = new WebPush();
		$vapid_generator = array('Minishlink\WebPush\VAPID', 'createVapidKeys');
    Perfecty_Push_Lib_Push_Server::bootstrap($webpush, $vapid_generator); 

    \Mockery::close();
    deactivate_perfecty_push();
		parent::tearDown();
	}

	/**
	 * Test the creation of the vapid keys
	 */
	public function test_vapid_keys_creation() {
    $mocked_server = Mockery::mock('webpush');

    $mocked_vapid_generator = Mockery::mock('vapid_generator');
    $mocked_vapid_generator
    ->shouldReceive('createVapidKeys')
    ->andReturn([
      'publicKey' => 'test_public_key',
      'privateKey' => 'test_private_key'])
    ->once();
    $mocked_vapid_callback = array($mocked_vapid_generator, 'createVapidKeys');

    Perfecty_Push_Lib_Push_Server::bootstrap($mocked_server, $mocked_vapid_callback); 

    $result = Perfecty_Push_Lib_Push_Server::create_vapid_keys();

    $expected = [
      'publicKey' => 'test_public_key',
      'privateKey' => 'test_private_key'
    ];
    $this->assertSame($expected, $result);
  }
  
  /**
   * Test sending one notification is successful
   */
  public function test_send_one_notification_success() {
    $mocked_server_result = Mockery::mock('result');
    $mocked_server_result
    ->shouldReceive('isSuccess')
    ->andReturn(true)
    ->once();

    $mocked_server = Mockery::mock('webpush');
    $mocked_server
    ->shouldReceive([
      'setReuseVAPIDHeaders' => true,
      'sendNotification' => true,
      'flush' => [$mocked_server_result]
    ])
    ->once();

    Perfecty_Push_Lib_Push_Server::bootstrap($mocked_server, $this->empty_callback); 

    $id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$subscriptions = [
      Perfecty_Push_Lib_Db::get_subscription($id)
    ];
    $result = Perfecty_Push_Lib_Push_Server::send_notification("this_is_the_payload", $subscriptions);

    $this->assertSame([1, 1], $result);
  }

  /**
   * Test sending one notification failed
   */
  public function test_send_one_notification_failed() {
    $mocked_server_result = Mockery::mock('result');
    $mocked_server_result
    ->shouldReceive([
      'isSuccess' => false,
      'getReason' => 'mocked reason'
    ])
    ->once();

    $mocked_server = Mockery::mock('webpush');
    $mocked_server
    ->shouldReceive([
      'setReuseVAPIDHeaders' => true,
      'sendNotification' => true,
      'flush' => [$mocked_server_result]
    ])
    ->once();

    Perfecty_Push_Lib_Push_Server::bootstrap($mocked_server, $this->empty_callback); 

    $id = Perfecty_Push_Lib_Db::store_subscription("my_endpoint_url", "my_key_auth", "my_p256dh_key", "127.0.0.1");
		$subscriptions = [
      Perfecty_Push_Lib_Db::get_subscription($id)
    ];
    $result = Perfecty_Push_Lib_Push_Server::send_notification("this_is_the_payload", $subscriptions);

    $this->assertSame([1, 0], $result);
  }
}