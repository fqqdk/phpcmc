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
	public function defaultOutputIsSummaryFormat()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/summary');
		$this->fsDriver->touch($this->workDir. '/summary/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/summary/OtherClass.php');

		$this->runner
			->on($this->absoluteWorkDir())
			->withDefaultOptions()
			->run();

		$this->runner->outputShows($this->correctHeader('@package_version@'));
		$this->runner->outputShows($this->classSummary(2));

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

		$output = $this->runner
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->run();

		$this->runner->parseOutputAsAssoc();
		$this->runner->classMapIs($this->assoc(array(
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

		$this->runner
			->on($this->absoluteWorkDir())
			->outputFormat('assoc')
			->namingConvention('parse')
			->run();

		$this->runner->parseOutputAsAssoc();
		$this->runner->errorContains(
			$this->logicalOr(
				$this->matchesRegularExpression(
					$this->duplicateErrorEntryPattern(
						'SomeClass',
						'duplicate/SomeFileWithSomeClass.php',
						'duplicate/OtherFileWithSomeClass.php'
					)
				),
				$this->matchesRegularExpression(
					$this->duplicateErrorEntryPattern(
						'SomeClass',
						'duplicate/OtherFileWithSomeClass.php',
						'duplicate/SomeFileWithSomeClass.php'
					)
				)
			)
		);

		$this->cleanupOnSuccess();
	}
}

?>