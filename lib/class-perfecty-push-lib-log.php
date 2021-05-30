<?php

/***
 * Logger
 */
class Perfecty_Push_Lib_Log {
	public const DEBUG   = 'debug';
	public const INFO    = 'info';
	public const WARNING = 'warning';
	public const ERROR   = 'error';

	private static $writer;
	private static $enabled = true;

	/**
	 * Sets the Log writer
	 *
	 * @param Perfecty_Push_Lib_Log_Writer $writer
	 */
	public static function init( Perfecty_Push_Lib_Log_Writer $writer ) {
		self::$writer = $writer;
	}

	/**
	 * Enable the logger
	 */
	public static function enable() {
		self::$enabled = true;
	}

	/**
	 * Disable the logger
	 */
	public static function disable() {
		self::$enabled = false;
	}

	/**
	 * Logs a debug message
	 *
	 * @param string $message Message to log
	 */
	public static function debug( $message ) {
		if ( ! self::$enabled ) {
			return;
		}
		self::$writer->write( self::DEBUG, $message );
	}

	/**
	 * Logs a info message
	 *
	 * @param string $message Message to log
	 */
	public static function info( $message ) {
		if ( ! self::$enabled ) {
			return;
		}
		self::$writer->write( self::INFO, $message );
	}

	/**
	 * Logs a warning message
	 *
	 * @param string $message Message to log
	 */
	public static function warning( $message ) {
		if ( ! self::$enabled ) {
			return;
		}
		self::$writer->write( self::WARNING, $message );
	}

	/**
	 * Logs an error message
	 *
	 * @param string $message Message to log
	 */
	public static function error( $message ) {
		if ( ! self::$enabled ) {
			return;
		}
		self::$writer->write( self::ERROR, $message );
	}
}
