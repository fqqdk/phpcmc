<?php
/**
 * Holds the PhpCmcRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Runner facility for the application
 */
class PhpCmcRunner
{
	/**
	 * @var PhpScriptRunner the script runner utility
	 */
	private $runner;

	/**
	 * @var Assert the assertion and constraint builder
	 */
	private $assert;

	/**
	 * @var string output of the script
	 */
	private $output;

	/**
	 * Constructor
	 *
	 * @param PhpScriptRunner $runner the script runner utility
	 * @param Assert          $assert the assertion and constraint builder
	 *
	 * @return PhpCmcRunner
	 */
	public function __construct(PhpScriptRunner $runner, Assert $assert)
	{
		$this->runner   = $runner;
		$this->assert   = $assert;
	}

	/**
	 * Runs the application in the given directory
	 *
	 * @param string $cmcScript   the script
	 * @param string $dir         the directory
	 * @param string $includePath the include_path to pass to the script
	 *
	 * @return string
	 */
	public function runInDirectory($cmcScript, $dir, $includePath='.')
	{
		return $this->output = $this->runner->runPhpScript($cmcScript, array($dir), $includePath);
	}

	/**
	 * The output as it should be included in a failure message
	 *
	 * @return string
	 */
	private function getOutput()
	{
		$result = '';
		foreach (explode(PHP_EOL, $this->output) as $line) {
			$result .= '> ' . $line . PHP_EOL;
		}
		return $result;
	}

	/**
	 * Asserts that the passed constraint evaluates for the output of the script
	 *
	 * @param PHPUnit_Framework_Constraint $constraint the constraint
	 *
	 * @return void
	 */
	public function outputShows($constraint)
	{
		$this->assert->that(
			$this->output,
			$constraint,
			'Erroneous script output : ' . PHP_EOL . $this->getOutput() . PHP_EOL
		);
	}

	/**
	 * Asserts that output of the script doesn't fulfill a constraint
	 *
	 * @param PHPUnit_Framework_Constraint $constraint the constraint
	 *
	 * @return void
	 */
	public function outputDoesNotShow($constraint)
	{
		$this->assert->that(
			$this->output,
			$this->assert->logicalNot($constraint)
		);
	}
}

?>