<?php
/**
 * Holds the OneClassFile class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * End to end tests for the application
 *
 * @acceptance
 */
class PhpCmcEndToEndTest extends PhooxTestCase
{
	/**
	 * Script runs and reports files from a single directory
	 * 
	 * @test
	 * 
	 * @return void
	 */
	public function collectsClassesFromDirectory()
	{
		$fsDriver = new FileSystemDriver(WORK_DIR);

		$fsDriver->rmdir('flatdir');
		$fsDriver->mkdir('flatdir');
		$fsDriver->touch('flatdir/SomeClass.php');
		$fsDriver->touch('flatdir/OtherClass.php');

		$assert = new Assert($this);
		$driver = new PhpCmcRunner(new PhpScriptRunner(), $assert);

		$driver->runInDirectory(BASE_DIR . 'src/phpcmc.php', $fsDriver->absolute('flatdir'));

		$driver->outputShows($this->correctHeader('@package_version@'));
		$driver->outputShows($this->aClassEntry('SomeClass',  'flatdir'));
		$driver->outputShows($this->aClassEntry('OtherClass', 'flatdir'));

		$fsDriver->rmdir('flatdir');
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
		$fsDriver = new FileSystemDriver(WORK_DIR);

		$fsDriver->rmdir('deepdir');
		$fsDriver->mkdir('deepdir');
		$fsDriver->mkdir('deepdir/one');
		$fsDriver->mkdir('deepdir/two');
		$fsDriver->touch('deepdir/one/SomeClass.php');
		$fsDriver->touch('deepdir/two/OtherClass.php');

		$assert = new Assert($this);
		$driver = new PhpCmcRunner(new PhpScriptRunner(), $assert);

		$driver->runInDirectory(BASE_DIR . 'src/phpcmc.php', $fsDriver->absolute('deepdir'));

		$driver->outputShows($this->correctHeader('@package_version@'));
		$driver->outputShows(self::aClassEntry('SomeClass',  'deepdir/one'));
		$driver->outputShows(self::aClassEntry('OtherClass', 'deepdir/two'));

		$fsDriver->rmdir('deepdir');
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
	public static function aClassEntry($className, $classFilePath)
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
			sprintf('phpcmc %s by fqqdk', $version)
		);
	}
}

?>