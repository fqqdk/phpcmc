<?php
/**
 * Holds the PackagingTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Test cases that test that the full building and packaging workflow is correct
 *
 * @group endtoend
 */
class PackagingTest extends ZetsuboTestCase
{
	/**
	 * @var PhpScriptRunner script runner
	 */
	private $runner;

	/**
	 * Sets up the fixtures
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->runner = new PhpScriptRunner();
	}

	/**
	 * Tests that ant can be called from the CLI
	 *
	 * @test
	 *
	 * @return void
	 */
	public function antIsAvailable()
	{
		$output = $this->runAntTasks(array('-version'),array());
		$this->assertRegExp('/Apache Ant version/',$output);
	}

	/**
	 * Tests that a package can be assembled, and installed in a PEAR repository
	 * and works from there
	 *
	 * @test
	 *
	 * @return void
	 */
	public function packageIsInstallable()
	{
		$fsDriver = new FileSystemDriver(WORK_DIR);

		$version        = '0.0.99';
		$targetDir     = 'targetdir/';
		$packageFile    = sprintf('%spackage/phpcmc-%s.tgz', $targetDir, $version);
		$pearConfigFile = 'pear-config';
		$repoDir        = 'repo/';
		$binDir         = $repoDir . 'pear/bin';
		$binFile        = $binDir . '/phpcmc';
		$isWin          = 'win' === substr(strtolower(PHP_OS), 0, 3);

		$classDir       = 'classes';
		$includeDir    = $repoDir . 'pear/php';

		$this->runAntTasks(array('clean', 'package'), array(
			'release.version' => $version,
			'target.dir'     => $fsDriver->absolute($targetDir),
			'uriAndOrChannel' => '<uri>file://' . $fsDriver->absolute($packageFile).'</uri>',
		));

		$this->assertTrue(
			$fsDriver->isReadable($packageFile),
			'generation of package file failed'
		);

		$fsDriver->rmdir($repoDir);
		$fsDriver->mkdir($repoDir);

		$this->runPearConfigCreate($fsDriver, $pearConfigFile, $repoDir, $isWin);

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
		$fsDriver->mkdir($classDir.'/base');
		$fsDriver->touch($classDir.'/base/SomeClass.php',  '<?php class SomeClass  {} ?'.'>');
		$fsDriver->touch($classDir.'/base/OtherClass.php', '<?php class OtherClass {} ?'.'>');

		$builder = new PhpCmcBuilder(new Assert($this));
		$driver  = new PhpCmcIsolatedRunner(
			$this->runner, $fsDriver->absolute($binFile)
		);

		$output = $builder
			->on($fsDriver->absolute($classDir))
			->includePath($fsDriver->absolute($includeDir))
			->outputFormat('assoc')
			->run($driver);

		$output->errorContains($this->isEmpty());
		$output->parsedOutputIs($this->assoc(array(
			'SomeClass'  => '/base/SomeClass.php',
			'OtherClass' => '/base/OtherClass.php',
		)));

		$this->runner->runPhpScriptFromStdin(
			sprintf('
				<?php
					define("PHPCMC_VERSION", "dummy");
					require_once "phpcmc/PhpCmcApi.php";
					PhpCmcApi::registerLoaderFor("%s");

					$foo = new SomeClass;
					$bar = new OtherClass;
				?>
			', $fsDriver->absolute($classDir.'/base')
			), array(
				'include_path'      => $fsDriver->absolute($repoDir.'pear/php')
			)
		);

		$fsDriver->rmdir($targetDir);
		$fsDriver->rmdir($repoDir);
		$fsDriver->unlink($pearConfigFile);
	}

	/**
	 * Runs ant with the specified tasks and properties in a separate process
	 *
	 * @param array $tasks      the list of the tasks
	 * @param array $properties the map of the properties
	 *
	 * @return string the output
	 */
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

	/**
	 * Runs the pear command with the supplied arguments
	 *
	 * @param array $args the arguments
	 *
	 * @return string the output
	 */
	private function runPear(array $args)
	{
		return ShellCommandBuilder::newScript('pear')
			->addArgs($args)
			->runWith($this->runner);
	}

	/**
	 * Runs 'pear install'
	 *
	 * @param string $pearConfigFile path to the config file
	 * @param string $packageFile    path to the package file
	 *
	 * @return string the output
	 */
	private function runPearInstall($pearConfigFile, $packageFile)
	{
		return $this->runPear(array('-c', $pearConfigFile, 'install', $packageFile));
	}

	/**
	 * Runs 'pear config-create' to create a separate pear repository for the
	 * tests
	 *
	 * @param FileSystemDriver $fsDriver   the filesystem driver
	 * @param string           $configFile the config file to create
	 * @param string           $repoDir    the directory of the repository
	 * @param boolean          $isWin      whether the underlying OS is windows
	 *
	 * @return string the output
	 */
	private function runPearConfigCreate($fsDriver, $configFile, $repoDir, $isWin)
	{
		$absRepoDir    = $fsDriver->absolute($repoDir);
		$absConfigFile = $fsDriver->absolute($configFile);
		$absBinDir     = $fsDriver->absolute($repoDir . 'pear/bin');

		$createArgs = array('config-create');
		if ($isWin) {
			$createArgs []= '-w';
		}
		$createArgs []= $absRepoDir;
		$createArgs []= $absConfigFile;

		$this->runPear($createArgs);

		$this->runPear(array('-c', $absConfigFile, 'config-set', 'bin_dir', $absBinDir));
	}
}

?>