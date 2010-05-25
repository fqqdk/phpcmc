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
	 * Script runs
	 * 
	 * @test
	 * 
	 * @return void
	 */
	public function collectsClassesFromDirectory()
	{
		// given
		$workDir  = dirname(__file__) . '/workdir/';

		$fsDriver = new FileSystemDriver($workDir);

		$fsDriver->rmdir('foo');
		$fsDriver->mkdir('foo');
		$fsDriver->touch('foo/SomeClass.php');
		$fsDriver->touch('foo/OtherClass.php');

		$assert = new Assert($this);
		$driver = new PhpCmcRunner($fsDriver, new PhpScriptRunner($assert), $assert);

		$driver->run();

		$driver->outputShows($this->correctHeader());
		$driver->outputShows($this->aClassEntry('SomeClass',  'foo'));
		$driver->outputShows($this->aClassEntry('OtherClass', 'foo'));
	}

	public function aClassEntry($className, $classFilePath)
	{
		return $this->matchesRegularExpression(
			$this->classEntryPattern($className, $classFilePath)
		);
	}

	private function classEntryPattern($className, $classFilePath)
	{
		return '/'.preg_quote($className, '/').'.*=>.*'.preg_quote($classFilePath, '/').'/';
	}

	public function correctHeader()
	{
		return $this->stringContains('phpcmc 0.0.1 by fqqdk');
	}
}

?>