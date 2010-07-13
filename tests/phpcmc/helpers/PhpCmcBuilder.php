<?php
/**
 * Holds the PhpCmcRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Runner facility for the application
 */
class PhpCmcBuilder
{
	/**
	 * @var Assert assertion builder
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
	 * @var string the include path to specify when running the application
	 */
	private $includePath = '.';

	/**
	 * @var string directory where the script runs
	 */
	private $directory = '.';

	/**
	 * Constructor
	 *
	 * @param Assert $assert assertion builder
	 *
	 * @return PhpCmcBuilder
	 */
	public function  __construct(Assert $assert)
	{
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
	 * Sets the include_path that will be supplied to the script
	 *
	 * @param string $includePath the include path
	 * 
	 * @return PhpCmcRunner
	 */
	public function includePath($includePath='.')
	{
		$this->includePath = $includePath;
		return $this;
	}

	/**
	 * Assembles the arguments to pass to the application
	 *
	 * @return array
	 */
	protected function assemble()
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
	 * Runs the application using the runner with the built up options
	 *
	 * @param PhpCmcRunner $runner the application runner
	 * 
	 * @return PhpCmcOutput
	 */
	public function run(PhpCmcRunner $runner)
	{
		list($output, $error) = $runner->run(
			$this->includePath, $this->assemble()
		);
		
		return new PhpCmcOutput($this->assert, $output, $error);
	}
}

?>