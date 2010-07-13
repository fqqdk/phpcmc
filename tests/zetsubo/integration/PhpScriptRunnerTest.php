<?php
/**
 * Holds the PhpScriptRunnerTest
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Holds test cases for the script runner facilities
 *
 * @group integration
 */
class PhpScriptRunnerTest extends ZetsuboTestCase
{
	/**
	 * @var PhpScriptRunner the object under test
	 */
	private $runner;

	/**
	 * Sets up the fixtures
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->runner = new PhpScriptRunner();
	}

	/**
	 * Tests that the PHP script is actually executed
	 *
	 * @test
	 *
	 * @return void
	 */
	public function phpScriptIsRun()
	{
		$output = $this->runner->runPhpScriptFromStdin('<?php echo 42; ?'.'>');
		$this->assertEquals(42, $output);
	}

	/**
	 * Tests that the PHP script is called with the supplied arguments
	 *
	 * @test
	 *
	 * @return void
	 */
	public function scriptCallShouldPassArguments()
	{
		$argv = array(42);

		$output = $this->runner->runPhpScriptFromStdin(
			'<?php echo $_SERVER["argv"][1]; ?'.'>', array(), $argv
		);

		$this->assertEquals(42,$output);
	}

	/**
	 * Tests that the PHP script has $_ENV.
	 *
	 * @test
	 *
	 * @return void
	 */
	public function envIsAvailable()
	{
		$this->requireIniSetting('variables_order', $this->stringContains('E'));
		$output = $this->runner->runPhpScriptFromStdin(
			'<?php echo $_ENV["foo"]; ?'.'>',
			array(),
			array(),
			array('foo'=>'bar')
		);

		$this->assertEquals('bar', $output);
	}

	/**
	 * Tests that the PHP script inherits the environment variables from the
	 * parent process
	 *
	 * @test
	 *
	 * @return void
	 */
	public function scriptInheritsParentEnvByDefault()
	{
		$output = $this->runner->runPhpScriptFromStdin('<?php echo serialize(sort($_ENV)); ?'.'>');
		$this->assertEquals(sort($_ENV), unserialize($output));
	}

	/**
	 * Tests that the PHP script gets the supplied ini variables correctly
	 *
	 * @test
	 *
	 * @return void
	 */
	public function arbitraryIniVariableGetsPassed()
	{
		$output = $this->runner->runPhpScriptFromStdin(
			'<?php echo ini_get("include_path"); ?'.'>',
			// we use include_path as ini key because on lunix systems setting
			// a nonexistent, dummy ini key does nothing and include_path will
			// always exist for every PHP installation
			array('include_path' => 'bar;baz')
		);

		$this->assertEquals('bar;baz', $output);
	}

	/**
	 * Tests that the include path is correctly passed to the PHP script
	 *
	 * @test
	 *
	 * @return void
	 */
	public function includePathGetsPassedCorrectly()
	{
		$includePath = 'foo/bar' . PATH_SEPARATOR . 'bar/foo';
		$output      = $this->runner->runPhpScriptFromStdin(
			'<?php echo get_include_path(); ?'.'>',
			array('include_path' => $includePath)
		);

		$this->assertEquals($includePath, $output);
	}

	/**
	 * Tests that the core escapeshellargs() function works as expected
	 *
	 * @test
	 *
	 * @return void
	 */
	public function escapeshellargsCorrectlyEscapesShellArguments()
	{
		$this->assertRegExp(
			'#^([\'"])foo'.PATH_SEPARATOR.'bar\1$#',
			escapeshellarg('foo'.PATH_SEPARATOR.'bar')
		);
	}
}

?>