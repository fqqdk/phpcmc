<?php

class PhpScriptRunnerIntegrationTest extends PhooxTestCase {
	
	/**
	 * @test
	 */
	public function phpShouldBeRunAsBinary() {
		$runner = new PhpScriptRunner();
		$runner->run('php',array(), '<?php echo 42; ?>');
	}
	
	/**
	 * @test
	 */
	public function envShouldBeAvailable() {
		$this->markTestSkipped('php bug');
		$runner = new PhpScriptRunner();
		$output = $runner->run('php',array(), '<?php echo $_ENV["foo"]; ?>', array('foo'=>'bar'));
		$this->assertEquals('bar',$output);
	}

	/**
	 * @test
	 */
	public function scriptCallShouldPassArguments() {
		$argv = array(42);
		$runner = new PhpScriptRunner();
		$output = $runner->run(
			'php',
			array_merge(
				array(
					'--',
				),
				$argv
			),
			'<?php echo $_SERVER["argv"][1]; ?>'
		);
		$this->assertEquals(42,$output);
	}
	
	/**
	 * @test
	 */
	public function scriptCallShouldPassIncludePathCorrectly() {
		$this->markTestSkipped();
		$includePath = 'foo'.DIRECTORY_SEPARATOR.'bar'.PATH_SEPARATOR.'bar'.DIRECTORY_SEPARATOR.'foo';
		$runner = new PhpScriptRunner();
		$output = $runner->run(
			'php',
			array(
				'-d',
				'include_path='. $includePath,
			),
			'<?php echo get_include_path(); ?>'
		);
		$this->assertEquals($includePath,$output);
	}

	/**
	 * @test
	 */
	public function iniPassingShouldBeCorrect() {
		$this->markTestSkipped();
		$runner = new PhpScriptRunner();
		$output = $runner->run(
			'php',
			array(
				'-d',
				'error_log=something;other',
			),
			'<?php echo ini_get("error_log"); ?>'
		);
		$this->assertEquals("something;other",$output);
	}
	
	/**
	 * @test
	 */
	public function arbitraryIniVariableGetsPassed() {
		$this->markTestSkipped('PHP fucks this up, too... too bad');
		$runner = new PhpScriptRunner();
		$output = $runner->run(
			'php',
			array(
				'-d',
				'mysql.default_password=bar;baz',
			),
			'<?php echo ini_get("mysql.default_password"); ?>'
		);
		$this->assertEquals("bar;baz",$output);
	}

	/**
	 * @test
	 */
	public function escapeshellargsShouldWork() {
		$this->assertRegExp('#^([\'"])foo'.PATH_SEPARATOR.'bar\1$#', escapeshellarg('foo'.PATH_SEPARATOR.'bar'));
	}
}

?>