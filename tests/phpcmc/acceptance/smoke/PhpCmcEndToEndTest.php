<?php
/**
 * Holds the OneClassFile class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of OneClassFile
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
		$workDir  = dirname(__file__) . '/workdir/';

		$fsDriver = new FileSystemDriver($workDir);

		$fsDriver->rmdir('flatdir');
		$fsDriver->mkdir('flatdir');
		$fsDriver->touch('flatdir/SomeClass.php');
		$fsDriver->touch('flatdir/OtherClass.php');

		$assert = new Assert($this);
		$driver = new PhpCmcRunner($fsDriver, new PhpScriptRunner($assert), $assert);

		$driver->runInDirectory('flatdir');

		$driver->outputShows($this->correctHeader());
		$driver->outputShows($this->aClassEntry('SomeClass',  'flatdir'));
		$driver->outputShows($this->aClassEntry('OtherClass', 'flatdir'));

		$fsDriver->rmdir('flatdir');
	}

	/**
	 * 
	 * @test
	 *
	 * @return void
	 */
	public function collectsClassesRecursively()
	{
		$workDir  = dirname(__file__) . '/workdir/';

		$fsDriver = new FileSystemDriver($workDir);

		$fsDriver->rmdir('deepdir');
		$fsDriver->mkdir('deepdir');
		$fsDriver->mkdir('deepdir/one');
		$fsDriver->mkdir('deepdir/two');
		$fsDriver->touch('deepdir/one/SomeClass.php');
		$fsDriver->touch('deepdir/two/OtherClass.php');

		$assert = new Assert($this);
		$driver = new PhpCmcRunner($fsDriver, new PhpScriptRunner($assert), $assert);

		$driver->runInDirectory('deepdir');

		$driver->outputShows($this->correctHeader());
		$driver->outputShows($this->aClassEntry('SomeClass',  'deepdir/one'));
		$driver->outputShows($this->aClassEntry('OtherClass', 'deepdir/two'));

		$fsDriver->rmdir('deepdir');
	}

	public function aClassEntry($className, $classFilePath)
	{
		return $this->matchesRegularExpression(
			$this->classEntryPattern($className, $classFilePath)
		);
	}

	private function classEntryPattern($className, $classFilePath)
	{
		return '#.*'.preg_quote($className, '#').'.*'.preg_quote($classFilePath, '#').'.*#';
	}

	public function correctHeader()
	{
		return $this->stringContains('phpcmc 0.0.1 by fqqdk');
	}
}

?>