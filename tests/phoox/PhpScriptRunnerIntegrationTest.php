<?php

class PhpScriptRunnerIntegrationTest extends PhooxTestCase
{
	/**
	 * @var PhpScriptRunner the object under test
	 */
	private $runner;

	protected function setUp()
	{
		$this->runner = new PhpScriptRunner();
	}

	/**
	 * @test
	 */
	public function phpShouldBeRunAsBinary() {
		$output = $this->runner->runPhpScriptFromStdin('<?php echo 42; ?>');
		$this->assertEquals(42, $output);
	}

	/**
	 * @test
	 */
	public function scriptCallShouldPassArguments() {
		$argv = array(42);

		$output = $this->runner->runPhpScriptFromStdin(
			'<?php echo $_SERVER["argv"][1]; ?>', array(), $argv
		);

		$this->assertEquals(42,$output);
	}
	
	/**
	 * @test
	 */
	public function envIsAvailable()
	{
		$this->assertContains(
			'E', ini_get('variables_order'),
			'env vars should be registered in the php.ini'
		);
		$output = $this->runner->runPhpScriptFromStdin(
			'<?php echo $_ENV["foo"]; ?>',
			array(),
			array(),
			array('foo'=>'bar')
		);

		$this->assertEquals('bar', $output);
	}

	/**
	 * @test
	 */
	public function arbitraryIniVariableGetsPassed()
	{
		$output = $this->runner->runPhpScriptFromStdin(
			'<?php echo ini_get("mysql.default_password"); ?>',
			array('mysql.default_password' => 'bar;baz')
		);

		$this->assertEquals("bar;baz", $output);
	}

	/**
	 * @test
	 */
	public function includePathGetsPassedCorrectly()
	{
		$includePath = 'foo/bar' . PATH_SEPARATOR . 'bar/foo';
		$output      = $this->runner->runPhpScriptFromStdin(
			'<?php echo get_include_path(); ?>',
			array('include_path' => $includePath)
		);

		$this->assertEquals($includePath, $output);
	}

	/**
	 * @test
	 */
	public function escapeshellargsShouldWork()
	{
		$this->assertRegExp(
			'#^([\'"])foo'.PATH_SEPARATOR.'bar\1$#',
			escapeshellarg('foo'.PATH_SEPARATOR.'bar')
		);
	}
}

?>