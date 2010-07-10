<?php
/**
 * Holds the CliToolTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of CliToolTest
 */
class CliToolTest extends PhpCmcEndToEndTest
{

	/**
	 * Script prints a fancy header
	 *
	 * @test
	 *
	 * @return void
	 */
	public function defaultOutputEmitsCorrectHeader()
	{
		$this->initFileSystem();

		$this->runner
			->on($this->absoluteWorkDir())
			->withDefaultOptions()
			->run($this->script);

		$this->runner->outputShows($this->correctHeader('@package_version@'));

		$this->cleanupOnSuccess();
	}

	/**
	 * Script runs and reports files from a single directory
	 *
	 * @test
	 *
	 * @return void
	 */
	public function specifiedAssociativeOutputFormatIsRespected()
	{
		$this->requireIniSwitch('allow_url_include');

		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/flatdir');
		$this->fsDriver->touch($this->workDir. '/flatdir/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/flatdir/OtherClass.php');

		$output = $this->runner
			->on($this->absoluteWorkDir().'/flatdir')
			->outputFormat('assoc')
			->run($this->script);

		//is valid php
		$this->assert->that(
			count(token_get_all($output)), $this->greaterThan(1),
			'output should be valid php'
		);

		ob_start();
		$classMap = include 'data://application/php;encoding=utf-8,'.$output;
		$generatedOutput = ob_get_contents();
		ob_end_clean();

		$this->assert->that($generatedOutput, $this->isEmpty());

		$this->assert->that($classMap, $this->assoc(array(
			'SomeClass'  => $this->stringContains('flatdir'),
			'OtherClass' => $this->stringContains('flatdir'),
		)));

		$this->cleanupOnSuccess();
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
			->on($this->absoluteWorkDir().'/deepdir')
			->withDefaultOptions()
			->run($this->script);

		$this->runner->outputShows(self::aClassEntry('SomeClass',  'deepdir/one'));
		$this->runner->outputShows(self::aClassEntry('OtherClass', 'deepdir/two'));

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
		$this->fsDriver->mkdir($this->workDir . '/dir');
		$this->fsDriver->touch($this->workDir . '/dir/SomeClass.php');
		$this->fsDriver->touch($this->workDir . '/dir/NotAClass.xml');

		$this->runner
			->on($this->absoluteWorkDir())
			->withDefaultOptions()
			->run(BASE_DIR . 'src/phpcmc.php');

		$this->runner->outputShows(self::aClassEntry('SomeClass', 'dir'));
		$this->runner->outputDoesNotShow($this->stringContains('NotAClass'));

		$this->cleanupOnSuccess();
	}
}

?>