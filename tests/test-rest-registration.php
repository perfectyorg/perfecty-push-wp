<?php

use Minishlink\WebPush\WebPush;
use Ramsey\Uuid\Uuid;

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
    public function test_registration_new() {
        $registration = new Perfecty_Push_Users();
        $data        = array(
            'user' => array(
                'endpoint' => 'http://my_endpoint',
                'keys'     => array(
                    'auth'   => 'my_auth_key',
                    'p256dh' => 'my_p256dh_key',
                ),
            ),
        );

        $previous_users = Perfecty_Push_Lib_Db::get_users( 0, 5 );
        $res           = $registration->register( $data );
        $users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

        $expected = array(
            'endpoint'   => 'http://my_endpoint',
            'key_auth'   => 'my_auth_key',
            'key_p256dh' => 'my_p256dh_key',
            'remote_ip'  => '',
            'wp_user_id' => null,
        );

        $this->assertSame( 0, count( $previous_users ) );
        $this->assertSame( 1, count( $users ) );
        $this->assertSame(
            (array) $res,
            array(
                'uuid'    => $users[0]->uuid,
                'is_active' => (bool)$users[0]->is_active
            )
        );
        $this->assertArraySubset( $expected, (array) $users[0] );
    }

	/**
	 * Test the user registration
	 */
	public function test_registration_new_with_ip_address() {
        $options                         = get_option( 'perfecty_push' );
        $options['segmentation_enabled'] = 1;
        update_option( 'perfecty_push', $options );

		$registration = new Perfecty_Push_Users();
		$data        = array(
			'user' => array(
				'endpoint' => 'http://my_endpoint',
				'keys'     => array(
					'auth'   => 'my_auth_key',
					'p256dh' => 'my_p256dh_key',
				),
			),
		);

        $previous_users = Perfecty_Push_Lib_Db::get_users( 0, 5 );
		$res           = $registration->register( $data );
		$users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

		$expected = array(
			'endpoint'   => 'http://my_endpoint',
			'key_auth'   => 'my_auth_key',
			'key_p256dh' => 'my_p256dh_key',
			'remote_ip'  => '127.0.0.1',
            'wp_user_id' => null,
		);

        $this->assertSame( 0, count( $previous_users ) );
		$this->assertSame( 1, count( $users ) );
		$this->assertSame(
			(array) $res,
			array(
				'uuid'    => $users[0]->uuid,
                'is_active' => (bool)$users[0]->is_active
			)
		);
		$this->assertArraySubset( $expected, (array) $users[0] );
	}

    /**
     * Test the user registration when the it's a WP User
     */
    public function test_registration_new_logged_in_wp_user() {
        $registration = new Perfecty_Push_Users();
        $data        = array(
            'user' => array(
                'endpoint' => 'http://my_endpoint',
                'keys'     => array(
                    'auth'   => 'my_auth_key',
                    'p256dh' => 'my_p256dh_key',
                ),
            ),
        );

        // Create a WP user and set it as current
        // we need to recreate the nonce we created above in the setup
        $wp_user_id = $this->factory->user->create();
        wp_set_current_user( $wp_user_id );
        $_SERVER['HTTP_X_WP_NONCE'] = wp_create_nonce( 'wp_rest' );

        $previous_users = Perfecty_Push_Lib_Db::get_users( 0, 5 );
        $res           = $registration->register( $data );
        $users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

        $expected = array(
            'endpoint'   => 'http://my_endpoint',
            'key_auth'   => 'my_auth_key',
            'key_p256dh' => 'my_p256dh_key',
            'remote_ip'  => '',
            'wp_user_id' => $wp_user_id,
        );

        $this->assertSame( 0, count( $previous_users ) );
        $this->assertSame( 1, count( $users ) );
        $this->assertSame(
            (array) $res,
            array(
                'uuid'    => $users[0]->uuid,
                'is_active' => (bool)$users[0]->is_active
            )
        );
        $this->assertArraySubset( $expected, (array) $users[0] );
    }
    /**
     * Test the user registration with an existing user (key_auth, key_p256dh)
     */
    public function test_registration_existing_user_keys() {
        $id = Perfecty_Push_Lib_Db::create_user( 'http://my_endpoint1', 'my_key_auth', 'my_p256dh_key', '192.168.0.1' );
        $user = Perfecty_Push_Lib_Db::get_user($id);

        $registration = new Perfecty_Push_Users();
        $data        = array(
            'user' => array(
                'endpoint' => 'http://my_endpoint2',
                'keys'     => array(
                    'auth'   => 'my_key_auth',
                    'p256dh' => 'my_p256dh_key',
                ),
            ),
            'user_id' => $user->uuid
        );

        $previous_users = Perfecty_Push_Lib_Db::get_users( 0, 5 );
        $res            = $registration->register( $data );
        $users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

        $expected = array(
            'uuid'       => $user->uuid,
            'endpoint'   => 'http://my_endpoint2',
            'key_auth'   => 'my_key_auth',
            'key_p256dh' => 'my_p256dh_key',
            'remote_ip'  => '',
            'wp_user_id' => null,
        );

        $this->assertSame( 1, count( $previous_users ) );
        $this->assertSame( 1, count( $users ) );
        $this->assertSame(
            (array) $res,
            array(
                'uuid'    => $user->uuid,
                'is_active' => (bool)$users[0]->is_active
            )
        );
        $this->assertArraySubset( $expected, (array) $users[0] );
    }

    /**
     * Test the user registration with an existing user (uuid)
     */
    public function test_registration_existing_user_uuid() {
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

        $previous_users = Perfecty_Push_Lib_Db::get_users( 0, 5 );
        $res           = $registration->register( $data );
        $users = Perfecty_Push_Lib_Db::get_users( 0, 5 );

        $expected = array(
            'endpoint'   => 'http://my_endpoint',
            'key_auth'   => 'updated_my_auth_key',
            'key_p256dh' => 'updated_my_p256dh_key',
            'remote_ip'  => '',
            'wp_user_id' => null,
        );

        $this->assertSame( 1, count( $previous_users ) );
        $this->assertSame( 1, count( $users ) );
        $this->assertSame(
            (array) $res,
            array(
                'uuid'    => $users[0]->uuid,
                'is_active' => (bool)$users[0]->is_active
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
     * Test the user registration
     */
    public function test_registration_new_with_invalid_ip_address()
    {
        $options = get_option('perfecty_push');
        $options['segmentation_enabled'] = 1;
        update_option('perfecty_push', $options);

        $_SERVER['REMOTE_ADDR'] = '127.7.7.444';

        $registration = new Perfecty_Push_Users();
        $data = array(
            'user' => array(
                'endpoint' => 'http://my_endpoint',
                'keys' => array(
                    'auth' => 'my_auth_key',
                    'p256dh' => 'my_p256dh_key',
                ),
            ),
        );

        $res = $registration->register( $data );

        $expected = array(
            'validation_error' => array(
                0 => 'Unknown Ip address',
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
