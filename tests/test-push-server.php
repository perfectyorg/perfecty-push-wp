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

	private $mocked_vapid_callback = "md5";

	public function setUp() {
		parent::setUp();
		activate_perfecty_push();

		if ( ! defined( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY' ) && ! defined( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY' ) ) {
			define( 'PERFECTY_PUSH_VAPID_PRIVATE_KEY', 'test_private_key' );
			define( 'PERFECTY_PUSH_VAPID_PUBLIC_KEY', 'test_public_key' );
		}
	}

	public function tearDown() {
        set_error_handler(
            function ( $errno, $errstr, $errfile, $errline ) {
                if ( strpos( $errstr, 'gmp extension is not loaded' ) !== false ) {
                    return true;
                }
                return false; // we raise it to the next handler otherwise
            }
        );
		$webpush         = new WebPush();
        restore_error_handler();

		$vapid_generator = array( 'Minishlink\WebPush\VAPID', 'createVapidKeys' );
		Perfecty_Push_Lib_Push_Server::bootstrap( array(), $vapid_generator, $webpush );

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

		Perfecty_Push_Lib_Push_Server::bootstrap( array(), $mocked_vapid_callback, $mocked_server );

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
				'queueNotification' => true,
				'flush'            => array( $mocked_server_result ),
			)
		)
		->once();

		Perfecty_Push_Lib_Push_Server::bootstrap( array(), $this->mocked_vapid_callback, $mocked_server );

		$id            = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$users = array(
			Perfecty_Push_Lib_Db::get_user( $id ),
		);
		[$succeeded, $failed]        = Perfecty_Push_Lib_Push_Server::send_notification( 'this_is_the_payload', $users );

		$this->assertSame( 1, $succeeded );
		$this->assertSame( 0, $failed );
	}

    /**
     * Test sending one notification failed
     */
    public function test_send_one_notification_failed() {
        $mocked_response = Mockery::mock('response');
        $mocked_response
            ->shouldReceive(
                array(
                    'getStatusCode' => 503 //server temporary unavailable
                )
            )
            ->once();
        $mocked_server_result = Mockery::mock( 'result' );
        $mocked_server_result
            ->shouldReceive(
                array(
                    'isSuccess' => false,
                    'getReason' => 'mocked reason',
                    'getEndpoint' => 'my_endpoint_url',
                    'isSubscriptionExpired' => false,
                    'getResponse' => $mocked_response
                )
            )
            ->once();

        $mocked_server = Mockery::mock( 'webpush' );
        $mocked_server
            ->shouldReceive(
                array(
                    'queueNotification' => true,
                    'flush'            => array( $mocked_server_result ),
                )
            )
            ->once();

        Perfecty_Push_Lib_Push_Server::bootstrap( array(), $this->mocked_vapid_callback, $mocked_server );

        $id            = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
        $users = array(
            Perfecty_Push_Lib_Db::get_user( $id ),
        );
        [$succeeded, $failed]        = Perfecty_Push_Lib_Push_Server::send_notification( 'this_is_the_payload', $users );

        $this->assertSame( 0, $succeeded );
	    $this->assertSame( 1, $failed );
    }

	/**
	 * Test sending one notification failed
	 */
	public function test_send_one_notification_unauthorize_not_found() {
	    $mocked_response = Mockery::mock('response');
	    $mocked_response
            ->shouldReceive(
                array(
                    'getStatusCode' => 403 // unauthorized
                )
            )
        ->once();

		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive(
			array(
				'isSuccess' => false,
				'getReason' => 'mocked reason',
                'getEndpoint' => 'my_endpoint_url',
                'isSubscriptionExpired' => false,
                'getResponse' => $mocked_response
			)
		)
		->once();

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive(
			array(
				'queueNotification' => true,
				'flush'            => array( $mocked_server_result ),
			)
		)
		->once();

		Perfecty_Push_Lib_Push_Server::bootstrap( array(), $this->mocked_vapid_callback, $mocked_server );

		$id            = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
        $user_before = Perfecty_Push_Lib_Db::get_user($id);
		$users = array(
			Perfecty_Push_Lib_Db::get_user( $id ),
		);
		[$succeeded, $failed]        = Perfecty_Push_Lib_Push_Server::send_notification( 'this_is_the_payload', $users );

        $user_after = Perfecty_Push_Lib_Db::get_user($id);

        $this->assertSame( 0, $succeeded );
		$this->assertSame( 1, $failed );
		$this->assertNotSame(null, $user_before);
        $this->assertSame(null, $user_after);
	}

    /**
     * Test sending one notification to an expired subscription
     */
    public function test_send_one_notification_expired_subscription() {
        $mocked_server_result = Mockery::mock( 'result' );
        $mocked_server_result
            ->shouldReceive(
                array(
                    'isSuccess' => false,
                    'getReason' => 'mocked reason',
                    'getEndpoint' => 'my_endpoint_url',
                    'isSubscriptionExpired' => true,
                )
            )
            ->once();

        $mocked_server = Mockery::mock( 'webpush' );
        $mocked_server
            ->shouldReceive(
                array(
                    'queueNotification' => true,
                    'flush'            => array( $mocked_server_result ),
                )
            )
            ->once();

        Perfecty_Push_Lib_Push_Server::bootstrap( array(), $this->mocked_vapid_callback, $mocked_server );

        $id            = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
        $user_before = Perfecty_Push_Lib_Db::get_user($id);
        $users = array(
            Perfecty_Push_Lib_Db::get_user( $id ),
        );
        [$succeeded, $failed]        = Perfecty_Push_Lib_Push_Server::send_notification( 'this_is_the_payload', $users );

        $user_after = Perfecty_Push_Lib_Db::get_user($id);

        $this->assertSame( 0, $succeeded );
	    $this->assertSame( 1, $failed );
	    $this->assertNotSame(null, $user_before);
        $this->assertEquals(null, $user_after);
    }

	/**
	 * Test schedule broadcast async
	 */
	public function test_schedule_broadcast_async() {
		$payload         = array('this_is_the_payload');
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

		$payload         = array('this_is_the_payload');
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );
		$next            = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		$this->assertFalse( $next );
		$this->assertFalse( $notification_id );
	}

	/**
	 * Test the execution of a broadcast batch
	 */
	public function test_execute_broadcast_batch() {
		$payload = array('this_is_the_payload');

		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive( 'isSuccess' )
		->andReturn( true )
		->twice();

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive(
			array(
				'flush' => array( $mocked_server_result, $mocked_server_result),
			)
		)
		->once()
		->shouldReceive(
			array(
				'queueNotification' => true,
			)
		)
		->twice();

		// base data
		Perfecty_Push_Lib_Push_Server::bootstrap( array(), $this->mocked_vapid_callback, $mocked_server );
		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url2', 'my_key_auth2', 'my_p256dh_key2', '127.0.0.1' );
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );
		// simulate the cron doing its job
		wp_clear_scheduled_hook( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// fire execution that send all the notifications
		$res_first = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
		// no additional jobs should have been scheduled
		$next_after_first_execution = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// check that subsequent calls do nothing
		$res_false = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );

		// assertions
		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		$expected     = array(
			'payload'     => '["this_is_the_payload"]',
			'total'       => 2,
			'succeeded'   => 2,
			'last_cursor' => 2,
			'batch_size'  => 1500,
			'status'      => 'completed',
			'is_taken'    => 0,
		);

		$this->assertArraySubset( $expected, (array) $notification );
		$this->assertNotFalse( $res_first);
		$this->assertFalse( $res_false );
		$this->assertFalse( $next_after_first_execution );
	}

	/**
	 * Test the execution of two broadcast batches
	 */
	public function test_execute_broadcast_batch_multiple() {
		$payload = array('this_is_the_payload');

		$mocked_server_result = Mockery::mock( 'result' );
		$mocked_server_result
		->shouldReceive( 'isSuccess' )
		->andReturn( true )
		->times( 3 );

		$mocked_server = Mockery::mock( 'webpush' );
		$mocked_server
		->shouldReceive('flush')
		-> andReturnUsing(
			function() use ( $mocked_server_result ) {
				sleep(2);
				return array( $mocked_server_result, $mocked_server_result ); // first batch, 2 items
			}
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
				'queueNotification' => true, // total notifications: 3 items
			)
		)
		->times( 3 );

		// test setup
		$options               = get_option( 'perfecty_push' );
		$options['batch_size'] = 2;
		update_option( 'perfecty_push', $options );
		Perfecty_Push_Lib_Push_Server::bootstrap( array(), $this->mocked_vapid_callback, $mocked_server, 2 );
		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url2', 'my_key_auth2', 'my_p256dh_key2', '127.0.0.1' );
		Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url3', 'my_key_auth3', 'my_p256dh_key3', '127.0.0.1' );
		$notification_id = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );
		// simulate the cron doing its job
		wp_clear_scheduled_hook( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// fire execution, it should execute the first 2, with a time limit exceeded warning
		$res_first = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
		// the next execution should have been scheduled as a single event, $next_after_first_execution should NOT be false
		$next_after_first_execution = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// simulate the cron doing its job
		wp_clear_scheduled_hook( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );
		// second execution, the rest of the remaining entries to send
		$res_second = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );

		// there should not be a third execution
		$next_after_second_execution = wp_next_scheduled( 'perfecty_push_broadcast_notification_event', array( $notification_id ) );

		// we simulate another execution, should return false
		$res_false = Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );

		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );
		$expected     = array(
			'payload'     => '["this_is_the_payload"]',
			'total'       => 3,
			'succeeded'   => 3,
			'last_cursor' => 3,
			'batch_size'  => 2,
			'status'      => 'completed',
			'is_taken'    => 0,
		);

		$this->assertNotFalse( $next_after_first_execution );
		$this->assertFalse( $next_after_second_execution );
		$this->assertArraySubset( $expected, (array) $notification );
		$this->assertNotFalse( $res_first );
		$this->assertNotFalse( $res_second );
		$this->assertFalse( $res_false );
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

		// the notification status should be the same (scheduled)
		$notification = Perfecty_Push_Lib_Db::get_notification( $notification_id );

		$this->assertSame( false, $res );
		$this->assertSame( 'scheduled', $notification->status );
		$this->assertFalse( $next_after_execution );
	}
}
