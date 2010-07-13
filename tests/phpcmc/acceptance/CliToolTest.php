<?php
/**
 * Holds the CliToolTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of CliToolTest
 *
 * @group endtoend
 */
class CliToolTest extends PhpCmcEndToEndTest
{
	/**
	 * @var PhpCmcIsolatedRunner the application runner
	 */
	private $runner;

	/**
	 * @var string the include_path
	 */
	private $includePath;

	/**
	 * Sets up the fixtures
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->includePath = BASE_DIR . '/src';
		$this->runner      = new PhpCmcIsolatedRunner(
			new PhpScriptRunner(), BASE_DIR . 'src/phpcmc.php'
		);
	}

	/**
	 * Script prints a fancy header
	 *
	 * @test
	 *
	 * @return void
	 */
	public function defaultOutputIsSummaryFormat()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/summary');
		$this->fsDriver->touch($this->workDir. '/summary/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/summary/OtherClass.php');

		$output = $this->builder
			->on($this->absoluteWorkDir())
			->includePath($this->includePath)
			->run($this->runner);

		$output->errorContains($this->isEmpty());
		$output->outputShows($this->correctHeader('@package_version@'));
		$output->outputShows($this->classSummary(2));

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
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/assoc');
		$this->fsDriver->touch($this->workDir. '/assoc/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/assoc/OtherClass.php');

		$output = $this->builder
			->on($this->absoluteWorkDir())
			->includePath($this->includePath)
			->outputFormat('assoc')
			->run($this->runner);

		$output->errorContains($this->isEmpty());
		$output->parsedOutputIs($this->assoc(array(
			'SomeClass'  => '/assoc/SomeClass.php',
			'OtherClass' => '/assoc/OtherClass.php',
		)));

		$this->cleanupOnSuccess();
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function duplicateClassesAreReportedOnStdErr()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir . '/duplicate');
		$this->fsDriver->touch(
			$this->workDir . '/duplicate/SomeFileWithSomeClass.php',
			'<?php
				class SomeClass {}
			?'.'>'
		);
		$this->fsDriver->touch(
			$this->workDir . '/duplicate/OtherFileWithSomeClass.php',
			'<?php
				class SomeClass {}
			?'.'>'
		);

		$output = $this->builder
			->on($this->absoluteWorkDir())
			->includePath($this->includePath)
			->outputFormat('assoc')
			->namingConvention('parse')
			->run($this->runner);

		$output->errorContains(
			$this->logicalOr(
				$this->matchesRegularExpression(
					$this->duplicateErrorEntryPattern(
						'SomeClass',
						'duplicate'.DIRECTORY_SEPARATOR.'SomeFileWithSomeClass.php',
						'duplicate'.DIRECTORY_SEPARATOR.'OtherFileWithSomeClass.php'
					)
				),
				$this->matchesRegularExpression(
					$this->duplicateErrorEntryPattern(
						'SomeClass',
						'duplicate'.DIRECTORY_SEPARATOR.'OtherFileWithSomeClass.php',
						'duplicate'.DIRECTORY_SEPARATOR.'SomeFileWithSomeClass.php'
					)
				)
			)
		);

		$this->cleanupOnSuccess();
	}
}

?>