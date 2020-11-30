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

	private $mocked_vapid_callback = 'echo "mocked"';

	public function setUp() {
		parent::setUp();
		activate_perfecty_push();

		if ( ! defined( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY' ) && ! defined( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY' ) ) {
			define( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY', 'test_private_key' );
			define( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY', 'test_public_key' );
		}
	}

	public function tearDown() {
		$webpush         = new WebPush();
		$vapid_generator = array( 'Minishlink\WebPush\VAPID', 'createVapidKeys' );
		Perfecty_Push_Lib_Push_Server::bootstrap( $webpush, $vapid_generator );

		\Mockery::close();
		deactivate_perfecty_push();
		parent::tearDown();
	}

	/**
	 * Test the creation of the vapid keys
	 */
	public function test_vapid_keys_creation() {
		$mocked_server = Mockery::mock( 'webpush' );

		$mocked_vapid_generator = Mockery::mock( 'vapid_generator' );
		$mocked_vapid_generator
		->shouldReceive( 'createVapidKeys' )
		->andReturn(
			array(
				'publicKey'  => 'test_public_key',
				'privateKey' => 'test_private_key',
			)
		)
		->once();
		$mocked_vapid_callback = array( $mocked_vapid_generator, 'createVapidKeys' );

		Perfecty_Push_Lib_Push_Server::bootstrap( $mocked_server, $mocked_vapid_callback );

		$result = Perfecty_Push_Lib_Push_Server::create_vapid_keys();

		$expected = array(
			'publicKey'  => 'test_public_key',
			'privateKey' => 'test_private_key',
		);
		$this->assertSame( $expected, $result );
	}

	/**
	 * Test sending one notification is successful
	 */
	public function test_send_one_notification_success() {
		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive( 'isSuccess' )
		->andReturn( true )
		->once();

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive(
			array(
				'sendNotification' => true,
				'flush'            => array( $mocked_server_result ),
			)
		)
		->once();

		Perfecty_Push_Lib_Push_Server::bootstrap( $mocked_server, $this->mocked_vapid_callback );

		$id            = Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$users = array(
			Perfecty_Push_Lib_Db::get_user( $id ),
		);
		$result        = Perfecty_Push_Lib_Push_Server::send_notification( 'this_is_the_payload', $users );

		$this->assertSame( array( 1, 1 ), $result );
	}

	/**
	 * Test sending one notification failed
	 */
	public function test_send_one_notification_failed() {
		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive(
			array(
				'isSuccess' => false,
				'getReason' => 'mocked reason',
			)
		)
		->once();

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive(
			array(
				'sendNotification' => true,
				'flush'            => array( $mocked_server_result ),
			)
		)
		->once();

		Perfecty_Push_Lib_Push_Server::bootstrap( $mocked_server, $this->mocked_vapid_callback );

		$id            = Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$users = array(
			Perfecty_Push_Lib_Db::get_user( $id ),
		);
		$result        = Perfecty_Push_Lib_Push_Server::send_notification( 'this_is_the_payload', $users );

		$this->assertSame( array( 1, 0 ), $result );
	}

	/**
	 * Test schedule broadcast async
	 */
	public function test_schedule_broadcast_async() {
		$payload         = 'this_is_the_payload';
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );
		$next            = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		$this->assertNotFalse( $next );
	}

	/**
	 * Test schedule broadcast async fails
	 */
	public function test_schedule_broadcast_async_fails() {
		$options                         = get_option( 'perfecty_push' );
		$options['use_action_scheduler'] = true;
		update_option( 'perfecty_push', $options );

		$payload         = 'this_is_the_payload';
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );
		$next            = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		$this->assertFalse( $next );
		$this->assertFalse( $notification_id );
	}

	/**
	 * Test the execution of a broadcast batch
	 */
	public function test_execute_broadcast_batch() {
		$payload = 'this_is_the_payload';

		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive( 'isSuccess' )
		->andReturn( true )
		->twice();

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive(
			array(
				'flush' => array( $mocked_server_result, $mocked_server_result ),
			)
		)
		->once()
		->shouldReceive(
			array(
				'sendNotification' => true,
			)
		)
		->twice();

		// base data
		Perfecty_Push_Lib_Push_Server::bootstrap( $mocked_server, $this->mocked_vapid_callback );
		Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url2', 'my_key_auth2', 'my_p256dh_key2', '127.0.0.1' );
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );

		// fire execution
		// TODO We should not wait for two consecutive cron cycles to mark the notification as completed
		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
		// the next execution should have been scheduled as a single event, $next_after_first_execution should NOT be false
		$next_after_first_execution = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// here we assume that the cron has kicked in and cleared the previous event
		wp_clear_scheduled_hook( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
		// there should not be any scheduled events afterwards, $next_after_second_execution should BE false
		$next_after_second_execution = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// assertions
		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		$expected     = array(
			'payload'     => 'this_is_the_payload',
			'total'       => 2,
			'succeeded'   => 2,
			'last_cursor' => 2,
			'batch_size'  => 30,
			'status'      => 'completed',
			'is_taken'    => 0,
		);

		$this->assertArraySubset( $expected, (array) $notification );
		$this->assertNotFalse( $res );
		$this->assertNotFalse( $next_after_first_execution );
		$this->assertFalse( $next_after_second_execution );
	}

	/**
	 * Test the execution of two broadcast batches
	 */
	public function test_execute_broadcast_batch_multiple() {
		$payload = 'this_is_the_payload';

		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive( 'isSuccess' )
		->andReturn( true )
		->times( 3 );

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive(
			array(
				'flush' => array( $mocked_server_result, $mocked_server_result ), // first batch, 2 items
			)
		)
		->once()
		->shouldReceive(
			array(
				'flush' => array( $mocked_server_result ), // second batch, 1 item
			)
		)
		->once()
		->shouldReceive(
			array(
				'sendNotification' => true, // total notifications: 3 items
			)
		)
		->times( 3 );

		// test setup
		$options               = get_option( 'perfecty_push' );
		$options['batch_size'] = 2;
		update_option( 'perfecty_push', $options );
		Perfecty_Push_Lib_Push_Server::bootstrap( $mocked_server, $this->mocked_vapid_callback );
		Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url2', 'my_key_auth2', 'my_p256dh_key2', '127.0.0.1' );
		Perfecty_Push_Lib_Db::store_user( 'my_endpoint_url3', 'my_key_auth3', 'my_p256dh_key3', '127.0.0.1' );
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );

		// fire execution
		// TODO We should not wait for three consecutive cron cycles to mark the notification as completed
		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );

		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		$expected     = array(
			'payload'     => 'this_is_the_payload',
			'total'       => 3,
			'succeeded'   => 3,
			'last_cursor' => 3,
			'batch_size'  => 2,
			'status'      => 'completed',
			'is_taken'    => 0,
		);

		$this->assertArraySubset( $expected, (array) $notification );
		$this->assertNotFalse( $res );
	}

	/**
	 * Test the execution of a non existing notification
	 */
	public function test_execute_broadcast_batch_notification_not_found() {
		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( 22332323 );
		$this->assertSame( false, $res );
	}

	/**
	 * Test the execution of an already taken notification
	 */
	public function test_execute_broadcast_batch_notification_taken() {
		$notification_id = Perfecty_Push_Lib_Db::create_notification( 'my_payload', Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED, 35, 20 );
		Perfecty_Push_Lib_Db::take_notification( $notification_id );

		// fire the execution
		$res = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );

		// there should not be any execution afterwards
		$next_after_execution = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// should have been marked as failed
		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );

		$this->assertSame( false, $res );
		$this->assertSame( 'failed', $notification->status );
		$this->assertFalse( $next_after_execution );
	}
}
