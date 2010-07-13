<?php
/**
 * Holds the ZetsuboTestCaseTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Test cases that check that we don't destroy PHPUnit's internal behaviour
 * while amending it here and there
 *
 * @group integration
 */
class ZetsuboTestCaseTest extends ZetsuboTestCase
{
	/**
	 * Tests that the current error handler is PHPUnit's own error handler
	 *
	 * @test
	 *
	 * @return void
	 */
	public function errorHandlerIsIntact()
	{
		//acquiring reference
		$currentErrorHandler = set_error_handler(array($this, 'dummy'));
		restore_error_handler();
		$this->assertEquals(
			array('PHPUnit_Util_ErrorHandler', 'handleError'), 
			$currentErrorHandler
		);
	}
	/**
	 * Tests that autoloaders emit user errors so that execution never reaches
	 * the point where the script dies with a fatal error.
	 *
	 * @test
	 *
	 * @return void
	 */
	public function loaderHandlerIsNice()
	{
		try {
			new NonExistant;
		} catch(PHPUnit_Framework_Error $ex) {
			return;
		}
	}

	/**
	 * Dummy error handler
	 *
	 * @param int    $code    error code
	 * @param string $message error message
	 *
	 * @return boolean
	 */
	public function dummy($code, $message)
	{
	}
}

?>