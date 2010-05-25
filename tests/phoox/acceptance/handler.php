<?php

class HandlerStack {
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

$handler = new HandlerStack();

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


?>
