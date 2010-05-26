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
	private function runStaticMethodOfThisTestIsolated($method, array $env=array(), array $includePath=array(), array $argv=array())
	{
		$includePath = implode(PATH_SEPARATOR, array_merge(
			$includePath, explode(PATH_SEPARATOR, get_include_path())
		));
		$runner = new PhpScriptRunner();
		$runner->run(
			'php',
			array_merge(
				array(
					'-d',
					'include_path='. $includePath,
					'--',
				),
				$argv
			),
			sprintf(
				'<?php
					//HACK lying about static dependencies
					class PhooxTestCase {}
					require_once \'%s\';
					%s::%s();
				?>',
				__file__, __class__, $method
			),
			$env
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function bootstrapperWorkflowIsSound()
	{
		$this->markTestSkipped();
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->rmdir('bootstrapper');
		$fsDriver->mkdir('bootstrapper');
		$fsDriver->mkdir('bootstrapper/lib1');
		$fsDriver->touch('bootstrapper/lib1/FirstClass.php', '<?php class FirstClass extends SecondClass {} ?>');
		$fsDriver->mkdir('bootstrapper/lib2');
		$fsDriver->touch('bootstrapper/lib2/SecondClass.php', '<?php class SecondClass {} ?>');

		$env = array(
			'WORK_DIR' => $fsDriver->absolute('bootstrapper')
		);

		$this->runStaticMethodOfThisTestIsolated('bootstrapped', $env, array(PHOOX_DIR));

		$fsDriver->rmdir('bootstrapper');
	}

	public static function bootstrapped() {
		self::foreignFail(var_export($_SERVER['argv'],true));
		$workDir = $_SERVER['argv'][1];
		require_once 'Bootstrapper.php';
		Bootstrapper::bootstrap(array(
			$workDir . '/lib1/',
			$workDir . '/lib2/'
		));

		//this should be loaded
		new FirstClass;

		set_error_handler(array(__class__, 'foo'));
		error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
		new NonExistant;

		exit(1);
	}

	public static function foo($code, $message)
	{
		if (false !== strpos($message, 'Failed loading NonExistant')) {
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
			$this->runStaticMethodOfThisTestIsolated('handlerWorkFlow');
		} catch(ForeignError $ex) {
			$this->assertStringStartsWith(
				'PHP Notice:  error 6', $ex->getMessage(),
				'STDERR content was: ' . $ex->getMessage()
			);
			return;
		}
		$this->fail('Expected an error message with text "error 6" but none occured');
	}

	public static function handlerWorkFlow()
	{
		$handler = new BootStrapperTest_HandlerStack();

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

class BootStrapperTest_HandlerStack {
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