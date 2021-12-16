<?php
/**
 * Class RestGetUserTest
 *
 * @package Perfecty_Push
 */

use Perfecty_Push_External_Uuid as Uuid;

/**
 * Test the Perfecty_Push_Users class
 */
class RestGetUserTest extends WP_UnitTestCase {

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
	 * Test get user
	 */
	public function test_get_user() {
		$id           = Perfecty_Push_Lib_Db::create_user( 'my_endpoint_url', 'my_key_auth', 'my_p256dh_key', '127.0.0.1' );
		$user = Perfecty_Push_Lib_Db::get_user( $id );

		$data        = array(
			'user_id'   => $user->uuid,
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->get_user( $data );

		$this->assertSame(
            (array) $res,
            array(
				'uuid'   => $user->uuid,
			)
		);
	}

	/**
	 * Test get user not found
	 */
	public function test_get_user_not_found() {
	    $uuid = Uuid::uuid4();
		$data        = array(
			'user_id'   => $uuid->toString(),
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->get_user( $data );

		$this->assertSame((array) $res, array());
	}

	/**
	 * Test get user invalid
	 */
	public function test_get_user_invalid() {
		$data        = array(
			'user_id'   => '7777777-wrong-uuid',
		);
		$users = new Perfecty_Push_Users();
		$res         = $users->get_user( $data );

		$expected = array(
			'bad_request' => array(
				0 => 'Invalid user ID',
			),
		);

		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertArraySubset( $expected, $res->errors );
		$this->assertSame( 400, $res->error_data['bad_request']['status'] );
	}

	/**
	 * Test get user with invalid nonce
	 */
	public function test_get_user_invalid_nonce() {
        $uuid = Uuid::uuid4();
        $data        = array(
            'user_id'   => $uuid->toString(),
        );
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
		$mock->get_user( $data );
	}
}
