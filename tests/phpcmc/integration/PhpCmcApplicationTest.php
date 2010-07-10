<?php
/**
 * Holds the ApplicationTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ApplicationTest
 *
 * @group integration
 */
class PhpCmcApplicationTest extends PhpCmcEndToEndTest
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

		$this->runner = new PhpCmcMainRunner('', new Assert($this));
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
		$this->fsDriver->mkdir($this->workDir. '/dummy');
		$argv = array(
			null,
			$this->absoluteWorkDir().'/dummy'
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
	/**
	 * Tests that the application collects classes from source directories
	 * recursively
	 *
	 * @test
	 *
	 * @return void
	 */
	public function collectsClassesRecursively()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir . '/deepdir');
		$this->fsDriver->mkdir($this->workDir . '/deepdir/one');
		$this->fsDriver->mkdir($this->workDir . '/deepdir/two');
		$this->fsDriver->touch($this->workDir . '/deepdir/one/SomeClass.php');
		$this->fsDriver->touch($this->workDir . '/deepdir/two/OtherClass.php');

		$this->runner
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->run();
		$this->runner->parseOutputAsAssoc();
		$this->runner->classMapIs($this->assoc(array(
			'SomeClass'  => '/deepdir/one/',
			'OtherClass' => '/deepdir/two/',
		)));

		$this->cleanupOnSuccess();
	}

	/**
	 * Tests that the application collects only classes from .php files
	 *
	 * @test
	 *
	 * @return void
	 */
	public function collectsOnlyPhpFiles()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir . '/mixed');
		$this->fsDriver->touch($this->workDir . '/mixed/SomeClass.php');
		$this->fsDriver->touch($this->workDir . '/mixed/NotAClass.xml');

		$this->runner
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->run();

		$this->runner->parseOutputAsAssoc();
		$this->runner->classMapIs($this->logicalAnd(
			$this->arrayHasKeyWithValue('SomeClass', '/mixed/'),
			$this->logicalNot($this->arrayHasKey('NotAClass'))
		));

		$this->cleanupOnSuccess();
	}
}

?>