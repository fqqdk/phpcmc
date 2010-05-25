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
	private function runLocalPhpScript($scriptName, array $env=array()) {
		$runner = new PhpScriptRunner();
		$runner->run(
			'php',
			array(dirname(__file__) . '/' . $scriptName . '.php'),
			'',
			$env
		);
	}

	private function runMethodIsolatedAsMain($method, array $env=array(), array $includePath=array())
	{
		$runner = new PhpScriptRunner();
		$runner->run(
			'php',
			array(
				'-d', 
				'include_path='. implode(PATH_SEPARATOR, $includePath)
			),
			$this->tokenizeMethodForStdIn($method),
			$env
		);
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
	private function runStaticMethodOfThisTest($method, array $env=array(), array $includePath=array())
	{
		$includePath = implode(PATH_SEPARATOR, array_merge(
			$includePath, explode(PATH_SEPARATOR, get_include_path())
		));
		$runner = new PhpScriptRunner();
		$runner->run(
			'php',
			array(
				'-d',
				'include_path='. $includePath
			),
			sprintf(
				'<?php
					class PHPUnit_Framework_TestCase {}
					require_once \'%s\';
					require_once \'%s\';
					%s::%s();
				?>',
				BOOTSTRAP_FILE, __file__, __class__, $method
			),
			$env
		);
	}

	private function tokenizeMethodForStdIn($method)
	{
		$tokens = token_get_all(file_get_contents(__file__));

		for ($i = 0; $i < count($tokens); ++$i) {
			if (is_array($tokens[$i]) && T_STRING === $tokens[$i][0] && $method == $tokens[$i][1]) {
				$start = $i;
				break;
			}
		}

		if (!$start) {
			throw new Exception('Method not found');
		}

		for ($i = $start; $i < count($tokens); ++$i) {
			if (false == is_array($tokens[$i]) && $tokens[$i] === '{') {
				$start = $i;
				break;
			}
		}

		if (!$start) {
			throw new Exception('Method not found');
		}

		$stack = 0;
		for ($i = $start + 1; $i < count($tokens); ++$i) {
			if (false == is_array($tokens[$i]) && $tokens[$i] === '{') {
				++$stack;
				continue;
			}
	
			if (false == is_array($tokens[$i]) && $tokens[$i] === '}') {
				if (0 === $stack) {
					$end = $i;
					break;
				}
				--$stack;
				continue;
			}

		}

		if (!$end) {
			throw new Exception('Method not found');
		}

		$script = '';
		for ($i = $start + 1; $i < $end; ++$i) {
			if (is_array($tokens[$i])) {
				$script .= $tokens[$i][1];
			} else {
				$script .= $tokens[$i];
			}
		}
		return sprintf('<?php %s ?>', $script);
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
		$fsDriver->touch('bootstrapper/lib1/FirstClass.php', '<?php class FirstClass {} ?>');
		$fsDriver->mkdir('bootstrapper/lib2');
		$fsDriver->touch('bootstrapper/lib2/SecondClass.php', '<?php class SecondClass extends FirstClass {} ?>');

		$env = array(
			'WORK_DIR' => $fsDriver->absolute('bootstrapper')
		);

		$this->runMethodIsolatedAsMain('bootstrapped', $env, array(
			PHOOX_DIR
		));

		$fsDriver->rmdir('bootstrapper');
	}

	public static function bootstrapped() {
		require_once 'Bootstrapper.php';
		Bootstrapper::bootstrap(array(
			$_ENV['WORK_DIR'] . '/lib1/',
			$_ENV['WORK_DIR'] . '/lib2/'
		));

		new FirstClass;

		function foo($code, $message)
		{
			if (false !== strpos($message, 'Failed loading NonExistant')) {
				exit (0);
			}
		}

		set_error_handler('foo');
		error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
		new NonExistant;

		exit(1);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function errorHandlerRegistrationOrderIsStackLike()
	{
		try {
			$this->runStaticMethodOfThisTest('handlerWorkFlow');
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

	private function fail($message) {
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_contents();
		ob_end_clean();

		fwrite(STDERR, $message . PHP_EOL . $trace);
		exit(1);
	}

	private function assertOrder($func, $message, array $order) {
		$errorId = $message{6};

		if (!is_numeric($errorId)) {
			$this->fail('Unexpected error: ' . $message);
		}

		if (in_array(++$this->order, $order)) {
			return;
		}

		$this->fail(sprintf(
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