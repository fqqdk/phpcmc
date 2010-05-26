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

//		$fsDriver->rmdir($packageDir);
//		$fsDriver->mkdir($packageDir);

		$this->runAntTasks(array('clean', 'package'), array(
			'release.version' => $version,
			'package.dir'     => $fsDriver->absolute($packageDir),
			'uriAndOrChannel' => '<uri>file://' . $fsDriver->absolute($packageFile).'</uri>',
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

		$fsDriver->rmdir($packageDir);
		$fsDriver->rmdir($repoDir);

		$fsDriver->unlink($pearConfigFile);
	}

	private function runPearInstall($pearConfigFile, $packageFile)
	{
		$args   = array('-c', $pearConfigFile, 'install', $packageFile);
		$output = $this->runner->run('pear', $args, '', $_ENV, false);
	}

	private function runAntTasks(array $tasks, array $properties)
	{
		$argv = $tasks;
		foreach ($properties as $propertyName => $propertyValue) {
			$argv[] = sprintf('-D%s=%s', $propertyName, $propertyValue);
		}
		return $this->runner->run('ant', $argv, '', $_ENV, false);
	}

	private function runPearConfigCreate($repoDir, $configFile, $binDir)
	{
		$configCreate = array('config-create', $repoDir, $configFile);
		$configSet    = array('-c', $configFile,'config-set', 'bin_dir', $binDir);

		$this->runner->run('pear', $configCreate, '', $_ENV, false);
		$this->runner->run('pear', $configSet, '', $_ENV, false);
	}
}

?>