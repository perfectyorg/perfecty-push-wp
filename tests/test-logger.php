<?php

/**
 * Class LoggerTest
 *
 * @package Perfecty_Push
 */

/**
 * Test the Perfecty_Push_Lib_Logger class
 */
class LoggerTest extends WP_UnitTestCase
{

    public function setUp()
    {
        parent::setUp();
        activate_perfecty_push();
    }

    public function tearDown()
    {
        deactivate_perfecty_push();
        parent::tearDown();
    }

    /**
     * Test logger
     */
    public function test_logger_enabled()
    {
    	$writter = new Perfecty_Push_Lib_Log_Db();
    	Perfecty_Push_Lib_Log::init($writter, Perfecty_Push_Lib_Log::DEBUG);
        Perfecty_Push_Lib_Log::enable();
        Perfecty_Push_Lib_Log::debug("debug message");
        Perfecty_Push_Lib_Log::info("info message");
        Perfecty_Push_Lib_Log::warning("warning message");
        Perfecty_Push_Lib_Log::error("error message");
        $logs = Perfecty_Push_Lib_Db::get_logs(0, 10);
        Perfecty_Push_Lib_Log::disable();

        $expected_debug = array(
            'level' => 'debug',
            'message' => 'debug message'
        );
        $expected_info = array(
            'level' => 'info',
            'message' => 'info message'
        );
        $expected_warning = array(
            'level' => 'warning',
            'message' => 'warning message'
        );
        $expected_error = array(
            'level' => 'error',
            'message' => 'error message'
        );
        $this->assertArraySubset( $expected_debug, (array) $logs[0]);
        $this->assertArraySubset( $expected_info, (array) $logs[1]);
        $this->assertArraySubset( $expected_warning, (array) $logs[2]);
        $this->assertArraySubset( $expected_error, (array) $logs[3]);
        $this->assertSame(4, count($logs));
    }

    /**
     * Test logger when disabled
     */
    public function test_logger_disabled()
    {
        Perfecty_Push_Lib_Log::disable();

        Perfecty_Push_Lib_Log::debug("debug message");
        Perfecty_Push_Lib_Log::info("info message");
        Perfecty_Push_Lib_Log::warning("warning message");
        Perfecty_Push_Lib_Log::error("error message");
        $logs = Perfecty_Push_Lib_Db::get_logs(0, 10);

        $this->assertSame(0, count($logs));
    }
}