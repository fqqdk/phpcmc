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
class PhpCmcEndToEndTest extends PhooxTestCase
{
	/**
	 * @var string the working directory of the script
	 */
	protected $workDir;

	/**
	 * @var string the phpcmc script to run in the tests
	 */
	protected $script;

	/**
	 * @var FileSystemDriver the filesystem driver
	 */
	protected $fsDriver;

	/**
	 * @var Assert the assertion builder
	 */
	protected $assert;

	/**
	 * @var PhpCmcRunner the script runner
	 */
	protected $runner;

	/**
	 * Sets the fixtures up
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		$this->workDir = get_class() . '/' . $this->name;
		if ($this->dataName || 0 === $this->dataName) {
			//TODO dataName should be escaped
			$this->workDir .= '/' . $this->dataName;
		}

		$this->script   = BASE_DIR . 'src/phpcmc.php';
		$this->fsDriver = new FileSystemDriver(WORK_DIR);
		$this->assert   = new Assert($this);
		$this->runner   = new PhpCmcRunner(new PhpScriptRunner(), $this->assert);

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
	public function collectsClasses()
	{
		$this->initFileSystem();
		$this->fsDriver->mkdir($this->workDir. '/flatdir');
		$this->fsDriver->touch($this->workDir. '/flatdir/SomeClass.php');
		$this->fsDriver->touch($this->workDir. '/flatdir/OtherClass.php');

		$this->runner
			->on($this->absoluteWorkDir().'/flatdir')
			->withDefaultOptions()
			->run($this->script);

		$this->runner->outputShows($this->aClassEntry('SomeClass',  'flatdir'));
		$this->runner->outputShows($this->aClassEntry('OtherClass', 'flatdir'));

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
}

?>