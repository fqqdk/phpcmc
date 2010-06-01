<?php
/**
 * Holds the PackagingTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of PackagingTest
 */
class PackagingTest extends PhooxTestCase
{
	private $runner;

	protected function setUp()
	{
		parent::setUp();
		$this->runner = new PhpScriptRunner();
	}

	/**
	 * @test
	 */
	public function antIsAvailable() {
		$output = $this->runAntTasks(array('-version'),array());
		$this->assertRegExp('/^Apache Ant version/',$output);
	}

	/**
	 * @test
	 */
	public function packageIsInstallable()
	{
		$fsDriver = new FileSystemDriver(WORK_DIR);

		$version        = '0.0.99';
		$packageDir     = 'packagedir/';
		$packageFile    = sprintf('%sphpcmc-%s.tgz', $packageDir, $version);
		$pearConfigFile = 'pear-config';
		$repoDir        = 'repo/';
		$binDir         = $repoDir . 'pear/bin';
		$binFile        = $binDir . '/phpcmc';
		$classDir       = 'classes';
		$includeDir    = $repoDir . 'pear/php';

		$this->runAntTasks(array('clean', 'package'), array(
			'release.version' => $version,
			'package.dir'     => $packageDir,
			'uriAndOrChannel' => '<uri>file://' . urlencode($fsDriver->absolute($packageFile)).'</uri>',
		));

		$this->assertTrue(
			$fsDriver->isReadable($packageFile),
			'generation of package file failed'
		);

		$fsDriver->rmdir($repoDir);
		$fsDriver->mkdir($repoDir);

		$this->runPearConfigCreate(
			$fsDriver->absolute($repoDir),
			$fsDriver->absolute($pearConfigFile),
			$fsDriver->absolute($binDir)
		);

		$this->assertTrue(
			$fsDriver->isReadable($pearConfigFile),
			'generation of pear configuration file failed'
		);

		$this->runPearInstall(
			$fsDriver->absolute($pearConfigFile),
			$fsDriver->absolute($packageFile)
		);

		$this->assertTrue(
			$fsDriver->isReadable($binFile),
			'the phpcmc executable is missing from the installation!'
		);

		$fsDriver->rmdir($classDir);
		$fsDriver->mkdir($classDir);
		$fsDriver->touch($classDir.'/SomeClass.php');
		$fsDriver->touch($classDir.'/OtherClass.php');

		$driver = new PhpCmcRunner($this->runner, new Assert($this));

		$driver->runInDirectory(
			$fsDriver->absolute($binFile),
			$fsDriver->absolute($classDir),
			$fsDriver->absolute($includeDir)
		);

		$driver->outputShows(PhpCmcEndToEndTest::correctHeader($version));
		$driver->outputShows(PhpCmcEndToEndTest::aClassEntry('SomeClass',  $classDir));
		$driver->outputShows(PhpCmcEndToEndTest::aClassEntry('OtherClass', $classDir));

//		$fsDriver->rmdir($packageDir);
//		$fsDriver->rmdir($repoDir);
//		$fsDriver->unlink($pearConfigFile);
	}

	private function runAntTasks(array $tasks, array $properties)
	{
		$antProperties = array();
		foreach ($properties as $propertyName => $propertyValue) {
			$antProperties['-D'.$propertyName] = $propertyValue;
		}

		return ShellCommandBuilder::newScript('ant')
			->addProperties($antProperties)
			->addArgs($tasks)
			->runWith($this->runner);
	}

	private function runPear(array $args)
	{
		return ShellCommandBuilder::newScript('pear')
			->addArgs($args)
			->runWith($this->runner);
	}

	private function runPearInstall($pearConfigFile, $packageFile)
	{
		return $this->runPear(array('-c', $pearConfigFile, 'install', $packageFile));
	}

	private function runPearConfigCreate($repoDir, $configFile, $binDir)
	{
		$this->runPear(array('config-create', $repoDir, $configFile));
		$this->runPear(array('-c', $configFile,'config-set', 'bin_dir', $binDir));
	}
}

?>