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
		$this->requireIniSwitch('allow_url_include');

		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/assoc');
		$this->fsDriver->touch($this->workDir. '/assoc/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/assoc/OtherClass.php');

		$output = $this->runner
			->on($this->absoluteWorkDir().'/assoc')
			->outputFormat('assoc')
			->run();

		$this->runner->parseOutputAsAssoc();
		$this->runner->classMapIs($this->assoc(array(
			'SomeClass'  => $this->stringContains('assoc'),
			'OtherClass' => $this->stringContains('assoc'),
		)));

		$this->cleanupOnSuccess();
	}
}

?>