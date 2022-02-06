<?php

/***
 * Logger
 */
class Perfecty_Push_Lib_Log {
	public const DEBUG   = 0;
	public const INFO    = 1;
	public const WARNING = 2;
	public const ERROR   = 3;

	private static $writer;
	private static $enabled = true;
	private static $level   = self::ERROR;

	/**
	 * Sets the Log writer
	 *
	 * @param Perfecty_Push_Lib_Log_Writer $writer
	 * @param int                          $level Error level
	 */
	public static function init( Perfecty_Push_Lib_Log_Writer $writer, $level = self::ERROR ) {
		self::$writer = $writer;
		self::$level  = $level;
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
	 * @param string $message Message to log.
	 */
	public static function debug( $message ) {
		if ( ! self::$enabled || self::$level > self::DEBUG ) {
			return;
		}
		self::$writer->write( self::level_to_string( self::DEBUG ), $message );
	}

	/**
	 * Logs a info message
	 *
	 * @param string $message Message to log.
	 */
	public static function info( $message ) {
		if ( ! self::$enabled || self::$level > self::INFO ) {
			return;
		}
		self::$writer->write( self::level_to_string( self::INFO ), $message );
	}

	/**
	 * Logs a warning message
	 *
	 * @param string $message Message to log.
	 */
	public static function warning( $message ) {
		if ( ! self::$enabled || self::$level > self::WARNING ) {
			return;
		}
		self::$writer->write( self::level_to_string( self::WARNING ), $message );
	}

	/**
	 * Logs an error message
	 *
	 * @param string $message Message to log.
	 */
	public static function error( $message ) {
		if ( ! self::$enabled || self::$level > self::ERROR ) {
			return;
		}
		self::$writer->write( self::level_to_string( self::ERROR ), $message );
	}

	/**
	 * Get the string from the level code
	 *
	 * @param $level int Level code.
	 *
	 * @return string
	 * @since 1.3.3
	 */
	private static function level_to_string( $level ) {
		$string = '';
		switch ( $level ) {
			case self::DEBUG:
				$string = 'debug';
				break;
			case self::INFO:
				$string = 'info';
				break;
			case self::WARNING:
				$string = 'warning';
				break;
			case self::ERROR:
				$string = 'error';
				break;
			default:
				break;
		}
		return $string;
	}

	/**
	 * Get the level code from the string
	 *
	 * @param $level_string string Level string.
	 *
	 * @return int
	 * @since 1.6.0
	 */
	public static function string_to_level( $level_string ) {
		$level = self::ERROR;
		switch ( $level_string ) {
			case 'debug':
				$level = self::DEBUG;
				break;
			case 'info':
				$level = self::INFO;
				break;
			case 'warning':
				$level = self::WARNING;
				break;
			default:
				break;
		}
		return $level;
	}
}
