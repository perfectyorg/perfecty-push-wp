<?php

/**
 * Class Perfecty_Push_External_Uuid
 *
 * UUID external wrapper
 */
class Perfecty_Push_External_Uuid {

	/**
	 * Check if the UUID is valid
	 *
	 * @return bool
	 */
	public static function isValid( $uuid ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		return Ramsey\Uuid\Uuid::isValid( $uuid );
	}

	/**
	 * Return a new uuid4
	 *
	 * @return mixed
	 */
	public static function uuid4() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		return Ramsey\Uuid\Uuid::uuid4();
	}
}
