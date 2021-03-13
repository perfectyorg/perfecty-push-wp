<?php

use Minishlink\WebPush\WebPush;

/**
 * Class RestRegistrationTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Users class
 */

class RestRegistrationTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		activate_perfecty_push();
        $_SERVER['HTTP_X_WP_NONCE'] = wp_create_nonce( 'wp_rest' );
	}

	public function tearDown() {
		\Mockery::close();
		deactivate_perfecty_push();
        unset( $_SERVER['HTTP_X_WP_NONCE'] );
		parent::tearDown();
	}

	/**
	 * Test the user registration
	 */
	public function test_registration() {
		$users = new Perfecty_Push_Users();
		$data        = array(
			'user' => array(
				'endpoint' => 'http://my_endpoint',
				'keys'     => array(
					'auth'   => 'my_auth_key',
					'p256dh' => 'my_p256dh_key',
				),
			),
		);

		$res           = $users->register( $data );
		$users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

		$expected = array(
			'endpoint'   => 'http://my_endpoint',
			'key_auth'   => 'my_auth_key',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip'  => '127.0.0.1',
		);

		$this->assertSame( 1, count( $users ) );
		$this->assertSame(
			(array) $res,
			array(
				'success' => true,
				'uuid'    => $users[0]->uuid,
			)
		);
		$this->assertArraySubset( $expected, (array) $users[0] );
	}

    /**
     * Test the user registration with an existing user
     */
    public function test_registration_existing_user() {
        $id = Perfecty_Push_Lib_Db::create_user( 'http://my_endpoint', 'my_key_auth', 'my_p256dh_key', '192.168.0.1' );
        $user = Perfecty_Push_Lib_Db::get_user($id);

        $registration = new Perfecty_Push_Users();
        $data        = array(
            'user' => array(
                'endpoint' => 'http://my_endpoint',
                'keys'     => array(
                    'auth'   => 'updated_my_auth_key',
                    'p256dh' => 'updated_my_p256dh_key',
                ),
            ),
            'user_id' => $user->uuid
        );

        $res           = $registration->register( $data );
        $users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

        $expected = array(
            'endpoint'   => 'http://my_endpoint',
            'key_auth'   => 'updated_my_auth_key',
            'key_p256dh' => 'updated_my_p256dh_key',
            'remote_ip'  => '127.0.0.1',
        );

        $this->assertSame( 1, count( $users ) );
        $this->assertSame(
            (array) $res,
            array(
                'success' => true,
                'uuid'    => $users[0]->uuid,
            )
        );
        $this->assertArraySubset( $expected, (array) $users[0] );
    }

	/**
	 * Test registration invalid
	 */
	public function test_registration_invalid() {
		$users = new Perfecty_Push_Users();
		$data        = array(
			'user' => array(
				'endpoint' => 'http://my_endpoint',
				'keys'     => array(
					'auth'   => '',
					'p256dh' => 'my_p256dh_key',
				),
			),
		);

		$res = $users->register( $data );

		$expected = array(
			'validation_error' => array(
				0 => 'Missing the auth key',
			),
		);

		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertArraySubset( $expected, $res->errors );
		$this->assertSame( 400, $res->error_data['validation_error']['status'] );
	}

	/**
	 * Test registration missing important data
	 */
	public function test_registration_missing_data() {
		$users = new Perfecty_Push_Users();
		$data        = array(
			'user' => array(
				'keys' => array(
					'p256dh' => 'my_p256dh_key',
				),
			),
		);

		$res = $users->register( $data );

		$expected = array(
			'validation_error' => array(
				0 => 'No endpoint was provided in the request',
			),
		);

		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertArraySubset( $expected, $res->errors );
		$this->assertSame( 400, $res->error_data['validation_error']['status'] );
	}

	/**
	 * Test registration DB error
	 */
	public function test_registration_db_error() {
		$users = new Perfecty_Push_Users();
		$data        = array(
			'user' => array(
				'endpoint' => 'http://my_endpoint',
				'keys'     => array(
					'auth'   => 'my_very_long_auth_key123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
					'p256dh' => 'my_p256dh_key',
				),
			),
		);

		$res = $users->register( $data );

		$expected = array(
			'failed_create' => array(
				0 => 'Could not subscribe the user',
			),
		);

		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertArraySubset( $expected, $res->errors );
		$this->assertSame( 500, $res->error_data['failed_create']['status'] );
	}

    /**
     * Test registration with an existing user, DB error
     */
    public function test_registration_existing_user_db_error() {
        $id = Perfecty_Push_Lib_Db::create_user( 'http://my_endpoint', 'my_key_auth', 'my_p256dh_key', '192.168.0.1' );
        $user = Perfecty_Push_Lib_Db::get_user($id);
        $registrations = new Perfecty_Push_Users();
        $data        = array(
            'user' => array(
                'endpoint' => 'http://my_endpoint',
                'keys'     => array(
                    'auth'   => 'my_very_long_auth_key123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
                    'p256dh' => 'my_p256dh_key',
                ),
            ),
            'user_id' => $user->uuid
        );

        $res = $registrations->register( $data );

        $expected = array(
            'failed_update' => array(
                0 => 'Could not update the user',
            ),
        );

        $this->assertInstanceOf( WP_Error::class, $res );
        $this->assertArraySubset( $expected, $res->errors );
        $this->assertSame( 500, $res->error_data['failed_update']['status'] );
    }

	/**
	 * Test the user registration with invalid nonce
	 */
	public function test_registration_invalid_nonce() {
		unset( $_SERVER['HTTP_X_WP_NONCE'] );
		$mock = Mockery::mock( Perfecty_Push_Users::class )->makePartial();
		$mock
		->shouldReceive( 'terminate' )
		->andReturnUsing(
			function() {
				throw new InvalidNonceException();
			}
		);

		$this->expectException( InvalidNonceException::class );
		$mock->register( array() );
	}
}
