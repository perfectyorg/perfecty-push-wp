<?php

/***
 * Log writter that uses error_log()
 */
class Perfecty_Push_Lib_Log_ErrorLog implements Perfecty_Push_Lib_Log_Writter {

	/**
	 * Write a message
	 *
	 * @param string $level Level code
	 * @param string $message Message to log
	 */
	public function write( $level, $message ) {
		error_log( strtoupper( $level ) . ' | ' . $message );
	}
}
