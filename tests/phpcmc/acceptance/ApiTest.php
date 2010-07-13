<?php
/**
 * Holds the ApiTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ApiTest
 */
class ApiTest extends PhpCmcEndToEndTest
{
	private $runner;

	public function setUp()
	{
		parent::setUp();
		$this->runner = new PhpScriptRunner();
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function itShouldRegisterAnAutoloaderThatCanFindClasses()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/clientsources');
		$this->fsDriver->touch(
			$this->workDir. '/clientsources/SomeClass.php',
			'<?php class SomeClass {} ?'.'>'
		);
		$this->fsDriver->touch(
			$this->workDir. '/clientsources/OtherClass.php',
			'<?php class OtherClass {} ?'.'>'
		);

		$this->runner->runPhpScriptFromStdin(
			sprintf('
				<?php
					require_once "phpcmc/PhpCmcApi.php";

					PhpCmcApi::registerLoaderOverSourceDir("%s");
					Bootstrapper::destroy();

					$foo = new SomeClass;
					$bar = new OtherClass;
				?>
			', $this->fsDriver->absolute($this->workDir . '/clientsources')
			), array(
				'include_path'      => realpath(SRC_DIR.'../'),
				'auto_prepend_file' => BOOTSTRAP_FILE
			)
		);

		$this->cleanupOnSuccess();
	}
}

?>