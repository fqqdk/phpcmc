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
	 * @var PhpCmcMainRunner the application runner
	 */
	private $runner;

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

		$this->runner = new PhpCmcMainRunner();
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

		$output = $this->builder
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->run($this->runner);

		$output->parsedOutputIs($this->assoc(array(
			'SomeClass'  => '/deepdir/one/SomeClass.php',
			'OtherClass' => '/deepdir/two/OtherClass.php',
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

		$output = $this->builder
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->run($this->runner);

		$output->parsedOutputIs($this->logicalAnd(
			$this->arrayHasKeyWithValue('SomeClass', '/mixed/SomeClass.php'),
			$this->logicalNot($this->arrayHasKey('NotAClass'))
		));

		$this->cleanupOnSuccess();
	}

	/**
	 * Tests that the application parses php files when the -nparse is given
	 *
	 * @test
	 *
	 * @return void
	 */
	public function parseNamingConventionParsesTheFiles()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir . '/parsing');
		$this->fsDriver->touch(
			$this->workDir . '/parsing/PhpFileWithoutClass.php',
			'<?php 
				// this file contains no classes
			?'.'>'
		);
		$this->fsDriver->touch(
			$this->workDir . '/parsing/PhpFileWithMultipleClasses.php',
			'<?php
				class SomeClass {}
				interface OtherClass {}
			?'.'>'
		);
		$this->fsDriver->touch(
			$this->workDir . '/parsing/PhpFileWithSyntaxError.php',
			'<?php
				class InvalidClass class class
			?'.'>'
		);

		$output = $this->builder
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->namingConvention('parse')
			->run($this->runner);

		$output->parsedOutputIs($this->logicalAnd(
			$this->arrayHasKeyWithValue('SomeClass', '/parsing/PhpFileWithMultipleClasses.php'),
			$this->arrayHasKeyWithValue('OtherClass', '/parsing/PhpFileWithMultipleClasses.php'),
			$this->logicalNot($this->arrayHasKey('PhpFileWithoutClass')),
			$this->logicalNot($this->arrayHasKey('PhpFileWithMultipleClasses')),
			$this->logicalNot($this->arrayHasKey('PhpFileWithSyntaxError')),
			$this->logicalNot($this->arrayHasKey('InvalidClass'))
		));

		$output->errorContains('/Parse error/');

		$this->cleanupOnSuccess();
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 * @expectedException PhpCmcException
	 *
	 * @return void
	 * @throws Exception
	 */
	public function runThrowsExceptionWhenNoDirectoryGiven()
	{
		$app = new PhpCmcApplication(new SpyStream(), new SpyStream());
		$app->run(array());
	}
}

?>