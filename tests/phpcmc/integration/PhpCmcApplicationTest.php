<?php
/**
 * Holds the ApplicationTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of ApplicationTest
 *
 * @group integration
 */
class ApplicationTest extends PhpCmcEndToEndTest
{
	/**
	 * Sets up the fixtures
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		if (false == defined('PHPCMC_VERSION')) {
			/**
			 * @final PHPCMC_VERSION dummy version instead of the constant
			 *                       defined in the application script
			 */
			define('PHPCMC_VERSION', 'dummy');
		}
	}
	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function mainMethodWorks()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/main');
		$this->fsDriver->touch($this->workDir. '/main/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/main/OtherClass.php');

		$argv = array(
			null,
			$this->absoluteWorkDir().'/main'
		);
		ob_start();
		PhpCmcApplication::main($argv);
		$output = ob_get_contents();
		ob_end_clean();

		$this->outputShows($output, $this->aClassEntry('SomeClass',  'main'));
		$this->outputShows($output, $this->aClassEntry('OtherClass', 'main'));

		$this->cleanupOnSuccess();
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function libraryIsSelfHosted()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/null');
		$argv = array(
			null,
			$this->absoluteWorkDir().'/null'
		);
		$library = PhpCmcApplication::library();

		$expectedDir = dirname($this->getFileOfClass('PhpCmcApplication'));
		$expectedDir = rtrim($expectedDir, DIRECTORY_SEPARATOR . '/');
		$actualDir   = rtrim($library[0], DIRECTORY_SEPARATOR . '/');
		$this->assertEquals($expectedDir, $actualDir);
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 * @expectedException PhpCmcException
	 *
	 * @return void
	 */
	public function mainThrowsExceptionWhenNoDirectoryGiven()
	{
		try {
			ob_start();
			PhpCmcApplication::main(array());
			ob_end_clean();
		} catch (Exception $e) {
			ob_end_clean();
			throw $e;
		}
	}
}

?>