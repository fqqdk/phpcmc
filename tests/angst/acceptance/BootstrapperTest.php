<?php
/**
 * Holds the BootstrapperTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Test cases for the common bootstrapper
 *
 * @group endtoend
 */
class BootstrapperTest extends ZetsuboTestCase
{
	/**
	 * Stops the execution of the process while prints debug information
	 *
	 * @param string $message the failure message
	 *
	 * @return void
	 */
	public static function foreignFail($message)
	{
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_contents();
		ob_end_clean();

		fwrite(fopen('php://stderr', 'w'), $message . PHP_EOL . $trace);
		exit(1);
	}

	/**
	 * Runs a function in a separate process
	 *
	 * @param string $function    the function
	 * @param string $file        a file that should be included for dependencies
	 * @param array  $env         environment variables
	 * @param array  $includePath include_path for the process
	 * @param array  $argv        arguments to pass to the process
	 *
	 * @return string the output of the process
	 */
	private function runFunctionIsolated(
		$function, $file, array $env=array(),
		array $includePath=array(), array $argv=array())
	{
		$script = sprintf(
			'<?php
				//HACK lying about static dependencies
				class ZetsuboTestCase {}
				require_once \'%s\';
				%s();
			?'.'>',
			$file, $function
		);
		$iniVars = array(
			'include_path' => implode(PATH_SEPARATOR, $includePath)
		);

		$runner = new PhpScriptRunner();
		return $runner->runPhpScriptFromStdin($script, $iniVars, $argv, $env);
	}

	/**
	 * Tests that the bootstrapper sets classloaders and errorhandlers correctly
	 *
	 * @test
	 *
	 * @return void
	 */
	public function bootstrapperWorkflowIsSound()
	{
		$this->requireIniSetting('variables_order', $this->stringContains('E'));
		$fsDriver = new FileSystemDriver(WORK_DIR);
		try {
			$fsDriver->rmdir('bootstrapper');
		} catch (UnexpectedValueException $ex) {
			// the dir didn't exist yet
		}

		$fsDriver->mkdir('bootstrapper');
		$fsDriver->mkdir('bootstrapper/lib1');
		$fsDriver->touch(
			'bootstrapper/lib1/FirstClass.php',
			'<?php class FirstClass extends SecondClass {} ?'.'>'
		);
		$fsDriver->mkdir('bootstrapper/lib2');
		$fsDriver->touch('bootstrapper/lib2/SecondClass.php', '<?php class SecondClass {} ?'.'>');
		$fsDriver->mkdir('bootstrapper/lib3');
		$fsDriver->touch('bootstrapper/lib3/ThirdClass.php',  '<?php class ThirdClass {} ?'.'>');
		$fsDriver->touch('bootstrapper/lib3/FourthClass.php', '<?php class FourthClass {} ?'.'>');

		$env = array(
			'WORK_DIR' => $fsDriver->absolute('bootstrapper')
		);

		$this->runFunctionIsolated(__class__.'::bootstrapped', __file__, $env, array(ANGST_DIR));

		$fsDriver->rmdir('bootstrapper');
	}

	/**
	 * Helper method for the workflow test case.
	 *
	 * @return void
	 */
	public static function bootstrapped()
	{
		$workDir  = $_ENV['WORK_DIR'];
		require_once 'Bootstrapper.php';
		$session = Bootstrapper::bootstrap(array(
			$workDir . '/lib1/',
			$workDir . '/lib2/',
		));

		// this should be loaded
		new FirstClass;

		$libThreeLoader = new DirLoader($session, $workDir . '/lib3/', __file__);
		$session->append($libThreeLoader);

		new ThirdClass;

		// errorhandlers shouldn't mess with the loaders
		set_error_handler(array(__class__, 'mockErrorHandler'));
		error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
		new FourthClass;

		exit(1);
	}

	/**
	 * Error handler function for the workflow test
	 *
	 * @param int    $code    the error code
	 * @param string $message the error message
	 *
	 * @return boolean
	 */
	public static function mockErrorHandler($code, $message)
	{
		if (false !== strpos($message, 'Failed loading FourthClass')) {
			exit (0);
		}
	}

	/**
	 * Tests that errorhandlers are in a stack-like structure
	 *
	 * @test
	 *
	 * @return void
	 */
	public function errorHandlerRegistrationOrderIsStackLike()
	{
		try {
			$this->runFunctionIsolated(__class__.'::handlerWorkFlow', __file__);
		} catch(ForeignError $ex) {
			$this->assertStringStartsWith(
				'PHP Notice:  error 6', $ex->getError(),
				'Unexpected error in script :' . $ex->getError()
			);
			return;
		}
		$this->fail('Expected an error message with text "error 6" but none occured');
	}

	/**
	 * Helper method for the error handler stack test case
	 *
	 * @return void
	 */
	public static function handlerWorkFlow()
	{
		$handler = new FakeHandlerStack();

		set_error_handler(array($handler, 'first'));
		trigger_error('error 1');

		set_error_handler(array($handler, 'second'));
		trigger_error('error 2');

		restore_error_handler();
		trigger_error('error 3');

		set_error_handler(array($handler, 'third'));
		trigger_error('error 4');

		restore_error_handler();
		trigger_error('error 5');

		restore_error_handler();
		trigger_error('error 6');
	}
}

/**
 * Helper class for the error handler stack test case
 */
class FakeHandlerStack
{
	/**
	 * @var int the number of calls to any of the handler methods
	 */
	private $callCount = 0;

	/**
	 * Asserts that the call to one errorhandler is
	 *
	 * @param string $method  the method that was called
	 * @param string $message the error message
	 * @param array  $order   the expected callcounts for the function
	 *
	 * @return void
	 */
	private function assertOrder($method, $message, array $order)
	{
		$errorId = substr($message, -1);

		if (!is_numeric($errorId)) {
			BootstrapperTest::foreignFail('Unexpected error: ' . $message);
		}

		if (in_array(++$this->callCount, $order)) {
			return;
		}

		BootstrapperTest::foreignFail(sprintf(
			'Expected errorhandler %s '        . PHP_EOL .
			'to be called at one of these: %s' . PHP_EOL .
			'Actually called at %s',
			$method, '(' . implode(',', $order) . ')', $errorId
		));

		exit(1);
	}

	/**
	 * Error handler method number one
	 *
	 * @param int    $code    error code
	 * @param string $message error message
	 *
	 * @return void
	 */
	public function first($code, $message)
	{
		$this->assertOrder(__function__, $message, array(1, 3, 5));
	}

	/**
	 * Error handler method number two
	 *
	 * @param int    $code    error code
	 * @param string $message error message
	 *
	 * @return void
	 */
	public function second($code, $message)
	{
		$this->assertOrder(__function__, $message, array(2));
	}

	/**
	 * Error handler method number three
	 *
	 * @param int    $code    error code
	 * @param string $message error message
	 *
	 * @return void
	 */
	public function third($code, $message)
	{
		$this->assertOrder(__function__, $message, array(4));
	}
}

?>