<?php
/**
 * Holds the BootstrapperTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of BootstrapperTest
 */
class BootstrapperTest extends PhooxTestCase
{
	private function runLocalPhpScript($scriptName) {
		$runner = new PhpScriptRunner();
		$runner->run('php', array(dirname(__file__) . '/' . $scriptName . '.php'), '');
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function bootstrapperWorkflowIsSound()
	{
		$this->runLocalPhpScript('bootstrapped');
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function errorHandlerRegistrationOrderIsStackLike()
	{
		try {
			$this->runLocalPhpScript('handler');
		} catch(ForeignError $ex) {
			$this->assertStringStartsWith('PHP Notice:  error 6', $ex->getMessage());
			return;
		}
		$this->fail('Expected an error message with text "error 6" but none occured');
	}
}

?>