<?php
/**
 * Holds the TestIntegrationTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of TestIntegrationTest
 */
class TestIntegrationTest extends PhooxTestCase
{
	/**
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
	 *
	 * @test
	 *
	 * @return void
	 */
	public function loaderHandlerIsNice()
	{
		$this->markTestSkipped();
		try {
			new NonExistant;
		} catch(PHPUnit_Framework_Error $ex) {
			// this is it
		}
	}

	public function dummy($code, $message)
	{
	}
}

?>