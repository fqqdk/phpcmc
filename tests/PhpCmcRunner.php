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
	 * @var Assert the assertion and constraint builder
	 */
	private $assert;

	/**
	 * @var string the output format to specify
	 */
	private $outputFormat;

	/**
	 * @var string the naming convention to specify
	 */
	private $namingConvention;

	/**
	 * @var array the classmap parsed from the output
	 */
	private $classMap;

	/**
	 * @var string the phpcmc script to run in the tests
	 */
	private $script;

	/**
	 * @var string directory where the script runs
	 */
	protected $directory = '.';

	/**
	 * @var string output of the script
	 */
	protected $output;

	/**
	 * @var string error output of the script
	 */
	protected $error;

	/**
	 * Constructor
	 *
	 * @param string $script the script to run
	 * @param Assert $assert the assertion and constraint builder
	 *
	 * @return PhpCmcRunner
	 */
	public function __construct($script, Assert $assert)
	{
		$this->script = $script;
		$this->assert = $assert;
	}

	/**
	 * Fluent builder method to supply directory
	 *
	 * @param string $directory the directory
	 *
	 * @return PhpCmcRunner
	 */
	public function on($directory)
	{
		$this->directory = $directory;

		return $this;
	}

	/**
	 * Runs the application
	 *
	 * @param string $includePath the include path to be set for the application
	 * @param string $theScript   the path to the script
	 *
	 * @return string the output
	 */
	public function run($includePath='.', $theScript='')
	{
		try {
			return $this->output = $this->runFromCli($includePath, $theScript);
		} catch (ForeignError $ex) {
			$this->output = $ex->getOutput();
			$this->error  = $ex->getError();
		}
	}

	/**
	 * Runs the application in the given directory
	 *
	 * @param string          $includePath the include_path to pass to the script
	 * @param string          $theScript   the script
	 * @param PhpScriptRunner $runner      the script runner
	 *
	 * @return string the output
	 */
	protected function runFromCli($includePath='.', $theScript='', $runner=null)
	{
		if (empty($theScript)) {
			$theScript = $this->script;
		}

		if (null === $runner) {
			$runner = new PhpScriptRunner();
		}

		$args = $this->assembleArguments();

		return $this->output = $runner->runPhpScript($theScript, $args, array(), $includePath);
	}

	/**
	 * Tells the runner that the script should be run with default options
	 *
	 * @return PhpCmcRunner
	 */
	public function withDefaultOptions()
	{
		return $this;
	}

	/**
	 * Sets the output format option that will be supplied to the script
	 *
	 * @param string $format the format
	 *
	 * @return PhpCmcRunner
	 */
	public function outputFormat($format)
	{
		$this->outputFormat = $format;

		return $this;
	}

	/**
	 * Sets the naming convention option that will be supplied to the script
	 *
	 * @param string $namingConvention the naming convention
	 *
	 * @return PhpCmcRunner
	 */
	public function namingConvention($namingConvention)
	{
		$this->namingConvention = $namingConvention;

		return $this;
	}

	/**
	 * Assembles the arguments to pass to the application
	 *
	 * @return array
	 */
	protected function assembleArguments()
	{
		$args = array();

		if (null !== $this->outputFormat) {
			$args []= '-f' . $this->outputFormat;
		}

		if (null !== $this->namingConvention) {
			$args []= '-n' . $this->namingConvention;
		}

		$args []= $this->directory;

		return $args;
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
	 * @param string $scriptOutput the output
	 *
	 * @return array the classmap
	 */
	public function parseOutputAsAssoc()
	{
		$this->assert->requireIniSwitch('allow_url_include');
		$this->outputIsValidPhp();
		ob_start();

		$this->classMap   = include 'data://application/php;encoding=utf-8,'.$this->output;
		$sideEffectOutput = ob_get_contents();

		ob_end_clean();

		$this->assert->isEmpty($sideEffectOutput);

		return $this->classMap;
	}

	/**
	 * Checks that classmap fullfils the constraint
	 *
	 * @param mixed $constraint the constraint
	 *
	 * @return void
	 */
	public function classMapIs($constraint)
	{
		$constraint = $this->assert->wrap($constraint);

		$this->assert->that($this->classMap, $constraint);
	}
}

?>