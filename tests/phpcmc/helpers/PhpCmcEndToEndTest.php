<?php
/**
 * Holds the OneClassFile class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * End to end tests for the application
 *
 * @group acceptance
 */
abstract class PhpCmcEndToEndTest extends ZetsuboTestCase
{
	/**
	 * @var string the working directory of the script
	 */
	protected $workDir;

	/**
	 * @var FileSystemDriver the filesystem driver
	 */
	protected $fsDriver;

	/**
	 * @var PhpCmcBuilder the script runner
	 */
	protected $builder;

	/**
	 * Sets the fixtures up
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->workDir = get_class($this) . '/' . $this->name;
		if ($this->dataName || 0 === $this->dataName) {
			//TODO dataName should be escaped
			$this->workDir .= '/' . $this->dataName;
		}

		$this->fsDriver = new FileSystemDriver(WORK_DIR);
		$this->builder  = new PhpCmcBuilder($this->assert);
	}

	/**
	 * Initializes the filesystem for a test
	 *
	 * @return void
	 */
	protected function initFileSystem()
	{
		$this->fsDriver->rmdir($this->workDir);
		$this->fsDriver->mkdir($this->workDir);
	}

	/**
	 * Cleans up directories used by the test
	 *
	 * @return void
	 */
	protected function cleanupOnSuccess()
	{
		$this->fsDriver->rmdir($this->workDir);
	}

	/**
	 * The absolute path for the working directory of a test
	 *
	 * @return string
	 */
	protected function absoluteWorkDir()
	{
		return $this->fsDriver->absolute($this->workDir);
	}

	/**
	 * Assertion checking that a line of string corresponds to a class entry in
	 * the output of the application
	 *
	 * @param string $className     the name of the class
	 * @param string $classFilePath the expected path to the class file
	 *
	 * @return PHPUnit_Framework_Constraint
	 */
	public static function aClassEntry($className, $classFilePath='')
	{
		return PHPUnit_Framework_Assert::matchesRegularExpression(
			self::classEntryPattern(
				$className,
				str_replace(DIRECTORY_SEPARATOR, '/', $classFilePath)
			)
		);
	}

	/**
	 * Generates regex pattern for a class entry
	 *
	 * @param string $className     the name of the class
	 * @param string $classFilePath the expected path to the class file
	 *
	 * @return string
	 */
	public static function classEntryPattern($className, $classFilePath)
	{
		return '#.*'.preg_quote($className, '#').'.*'.preg_quote($classFilePath, '#').'.*#';
	}

	/**
	 * Generates regex pattern for an error entry
	 *
	 * @param string $className      the duplicate class
	 * @param string $firstFilePath  path to the first location of the class
	 * @param string $secondFilePath path to the location of the duplicate class
	 *
	 * @return string
	 */
	public static function duplicateErrorEntryPattern($className, $firstFilePath, $secondFilePath)
	{
		return sprintf(
			'#Duplicate class %s in .*%s, first defined in .*%s#',
			preg_quote($className, '#'),
			preg_quote($firstFilePath, '#'),
			preg_quote($secondFilePath, '#')
		);
	}

	/**
	 * Contraint that checks that the output of the application contains the
	 * correct header line indicating e.g. the version and author
	 *
	 * @param string $version expected version number
	 *
	 * @return PHPUnit_Framework_Constraint
	 */
	public static function correctHeader($version)
	{
		return PHPUnit_Framework_Assert::stringContains(
			sprintf('phpcmc %s by fqqdk, sebcsaba', $version)
		);
	}

	/**
	 * Constraint checking that a summary with the exact number of classes found
	 * is present in the output.
	 *
	 * @param int $classCount assumed count of classes
	 *
	 * @return PHPUnit_Framework_Constraint
	 */
	public static function classSummary($classCount)
	{
		return PHPUnit_Framework_Assert::stringContains(
			sprintf('found %s classes', $classCount)
		);
	}

	/**
	 * Asserts that the script output fulfills the given constraint
	 *
	 * @param string                       $output     the script output
	 * @param PHPUnit_Framework_Constraint $constraint the constraint
	 *
	 * @return void
	 */
	protected function outputShows($output, $constraint)
	{
		$this->assert->that(
			$output,
			$constraint,
			'Erroneous script output : ' . PHP_EOL . $this->getOutput($output) . PHP_EOL
		);
	}

	/**
	 * The output as it should be included in a failure message
	 *
	 * @param string $output the script output
	 *
	 * @return string
	 */
	private function getOutput($output)
	{
		$result = '';
		foreach (explode(PHP_EOL, $output) as $line) {
			$result .= '> ' . $line . PHP_EOL;
		}
		return $result;
	}
}

?>