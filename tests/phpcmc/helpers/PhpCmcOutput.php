<?php
/**
 * Holds the PhpCmcOutput class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcOutput
 */
class PhpCmcOutput
{
	/**
	 * @var Assert assertion builder
	 */
	private $assert;

	/**
	 * @var string the content of the script output
	 */
	private $output;

	/**
	 * @var string the content of the script error output
	 */
	private $error;

	/**
	 * Constructor
	 *
	 * @param Assert $assert assertion builder
	 * @param string $output the content of the script output
	 * @param string $error  the content of the script error output
	 *
	 * @return PhpCmcOutput
	 */
	public function __construct(Assert $assert, $output, $error)
	{
		$this->assert = $assert;
		$this->output = $output;
		$this->error  = $error;
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
	 * The output as it should be included in a failure message
	 *
	 * @return string
	 */
	private function getError()
	{
		$result = '';
		foreach (explode(PHP_EOL, $this->error) as $line) {
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
			'Invalid script output : ' . PHP_EOL . $this->getOutput() . PHP_EOL
		);
	}

	/**
	 * Asserts that the passed constraint evaluates for the error output of the script
	 *
	 * @param PHPUnit_Framework_Constraint $constraint the constraint
	 *
	 * @return void
	 */
	public function errorContains($constraint)
	{
		$this->assert->that(
			$this->error,
			$constraint,
			'Invalid script error output : ' . PHP_EOL . $this->getError() . PHP_EOL
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

	/**
	 * Checks that output is tokenizable
	 *
	 * @return void
	 */
	protected function outputIsValidPhp()
	{
		$tokens = token_get_all($this->output);
		$this->assert->that(
			count($tokens), $this->assert->greaterThan(1),
			sprintf('Output should be valid php, but found %s', PHP_EOL . $this->output)
		);
	}

	/**
	 * Attemts to parse script output as a php file containing an associative
	 * array
	 *
	 * @param PHPUnit_Framework_Constraint $constraint to match the classmap
	 *
	 * @return array the classmap
	 */
	public function parsedOutputIs($constraint)
	{
		$this->assert->requireIniSwitch('allow_url_include');
		$this->outputIsValidPhp();
		ob_start();

		$classMap   = include 'data://application/php;encoding=utf-8,'.$this->output;
		$sideEffectOutput = ob_get_contents();

		ob_end_clean();

		$this->assert->isEmpty($sideEffectOutput);

		$constraint = $this->assert->wrap($constraint);
		$this->assert->that($classMap, $constraint);
	}

	/**
	 * var_dump()'s the output and error of the script and exits
	 *
	 * @return void
	 */
	public function varDumpAndDie()
	{
		var_dump($this->output, $this->error); die;
	}
}

?>