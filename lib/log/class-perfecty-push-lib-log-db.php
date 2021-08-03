<?php

/***
 * Log writer that uses the DB
 */
class Perfecty_Push_Lib_Log_Db implements Perfecty_Push_Lib_Log_Writer {

	/**
	 * Insert a log entry in the DB
	 *
	 * @param string $level Level code.
	 * @param string $message Message to log.
	 */
	public function write( $level, $message ) {
		return Perfecty_Push_Lib_Db::insert_log( $level, $message );
	}
}
