<?php
/**
 * Class RestUpdatePreferencesTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Users class
 */
class RestUpdatePreferencesTest extends WP_UnitTestCase {

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
	 * Test set user inactive
	 */
	public function test_set_inactive() {
		$id           = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$user = Perfecty_Push_Lib_Db::get_user( $id );

		$data        = array(
			'is_active' => false,
			'user_id'   => $user->uuid,
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->update_preferences( $data );

		$updated_user = Perfecty_Push_Lib_Db::get_user( $id );
		$this->assertSame(
			(array) $res,
			array(
				'is_active' => false,
			)
		);

		$this->assertEquals( 1, $user->is_active );
		$this->assertEquals( 0, $updated_user->is_active );
	}

	/**
	 * Test set user active
	 */
	public function test_set_active() {
		$id = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::set_user_active( $id, false );
		$user = Perfecty_Push_Lib_Db::get_user( $id );

		$data        = array(
			'is_active' => true,
			'user_id'   => $user->uuid,
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->update_preferences( $data );

		$updated_user = Perfecty_Push_Lib_Db::get_user( $id );
		$this->assertSame(
			(array) $res,
			array(
				'is_active' => true,
			)
		);

		$this->assertEquals( 0, $user->is_active );
		$this->assertEquals( 1, $updated_user->is_active );
	}

	/**
	 * Test update preferences invalid
	 */
	public function test_update_preferences_invalid() {
		$id = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		Perfecty_Push_Lib_Db::set_user_active( $id, false );
		$user = Perfecty_Push_Lib_Db::get_user( $id );

		$data        = array(
			'is_active' => true,
			'user_id'   => '3',
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->update_preferences( $data );

		$expected = array(
			'bad_request' => array(
				0 => 'Invalid player ID',
			),
		);

		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertArraySubset( $expected, $res->errors );
		$this->assertSame( 400, $res->error_data['bad_request']['status'] );
	}

	/**
	 * Test update preferences when user not found
	 */
	public function test_update_preferences_user_not_found() {
		$data        = array(
			'is_active' => true,
			'user_id'   => '1139eea1-8ff4-4421-92ab-f8ac74cef616',
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->update_preferences( $data );

		$expected = array(
			'bad_request' => array(
				0 => 'user id not found',
			),
		);

		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertArraySubset( $expected, $res->errors );
		$this->assertSame( 404, $res->error_data['bad_request']['status'] );
	}

	/**
	 * Test update preferences with invalid nonce
	 */
	public function test_update_preferences_invalid_nonce() {
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
		$mock->update_preferences( array() );
	}
}
