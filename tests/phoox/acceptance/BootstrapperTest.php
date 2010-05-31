<?php
/**
 * Holds the BootstrapperTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of BootstrapperTest
 */
class BootstrapperTest extends PhooxTestCase
{
	public static function foreignFail($message) {
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_contents();
		ob_end_clean();

		fwrite(fopen('php://stderr', 'w'), $message . PHP_EOL . $trace);
		exit(1);
	}

	/**
	 * 
	 * @param string $method
	 * @param array  $env
	 * @param array  $includePath
	 *
	 * @todo lint!
	 *
	 * @return void
	 */
	private function runFunctionIsolated($function, $file, array $env=null, array $includePath=array(), array $argv=array())
	{
		$script = sprintf(
			'<?php
				//HACK lying about static dependencies
				class PhooxTestCase {}
				require_once \'%s\';
				%s();
			?>',
			$file, $function
		);
		$iniVars = array(
			'include_path' => implode(PATH_SEPARATOR, $includePath)
		);

		$runner = new PhpScriptRunner();
		$runner->runPhpScriptFromStdin($script, $iniVars, $argv, $env);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function bootstrapperWorkflowIsSound()
	{
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->rmdir('bootstrapper');
		$fsDriver->mkdir('bootstrapper');
		$fsDriver->mkdir('bootstrapper/lib1');
		$fsDriver->touch('bootstrapper/lib1/FirstClass.php', '<?php class FirstClass extends SecondClass {} ?>');
		$fsDriver->mkdir('bootstrapper/lib2');
		$fsDriver->touch('bootstrapper/lib2/SecondClass.php', '<?php class SecondClass {} ?>');
		$fsDriver->mkdir('bootstrapper/lib3');
		$fsDriver->touch('bootstrapper/lib3/ThirdClass.php',  '<?php class ThirdClass {} ?>');
		$fsDriver->touch('bootstrapper/lib3/FourthClass.php', '<?php class FourthClass {} ?>');

		$env = array(
			'WORK_DIR' => $fsDriver->absolute('bootstrapper')
		);

		$this->runFunctionIsolated(__class__.'::bootstrapped', __file__, $env, array(PHOOX_DIR));

		$fsDriver->rmdir('bootstrapper');
	}

	public static function bootstrapped() {
		$workDir = $_ENV['WORK_DIR'];
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
		set_error_handler(array(__class__, 'foo'));
		error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
		new FourthClass;

		exit(1);
	}

	public static function foo($code, $message)
	{
		if (false !== strpos($message, 'Failed loading FourthClass')) {
			exit (0);
		}
	}

	/**
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

class FakeHandlerStack {
	private $order = 0;

	private function assertOrder($func, $message, array $order) {
		$errorId = $message{6};

		if (!is_numeric($errorId)) {
			$this->fail('Unexpected error: ' . $message);
		}

		if (in_array(++$this->order, $order)) {
			return;
		}

		BootstrapperTest::foreignFail(sprintf(
			'Expected errorhandler %s '        . PHP_EOL .
			'to be called at one of these: %s' . PHP_EOL .
			'Actually called at %s',
			$func, '(' . implode(',', $order) . ')', $errorId
		));

		exit(1);
	}

	public function first($code, $message) {
		$this->assertOrder(__function__, $message, array(1, 3, 5));
	}

	public function second($code, $message) {
		$this->assertOrder(__function__, $message, array(2));
	}

	public function third($code, $message) {
		$this->assertOrder(__function__, $message, array(4));
	}
}

?>