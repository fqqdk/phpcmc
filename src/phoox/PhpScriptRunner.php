<?php
/**
 * Holds the PhpScriptRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpScriptRunner
 */
class PhpScriptRunner
{
	public function run($script, array $args, $stdin)
	{
		$stdinHandle = $this->open($stdin);

		$desc = array(
			0 => $stdinHandle,
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$cmd  = $this->cmd($script, $args);
		$proc = proc_open($cmd, $desc, $pipes);
		if (false === $proc) {
			trigger_error('cannot execute ' . $cmd);
		}

		$result = stream_get_contents($pipes[1]);

		$errors = stream_get_contents($pipes[2]);

		if (false == empty($errors)) {
			throw new ForeignError($errors);
		}

		fclose($pipes[1]);
		fclose($pipes[2]);
		fclose($stdinHandle);
		proc_close($proc);

		return $result;
	}

	private function cmd($script, array $args)
	{
		$result = $script;

		foreach($args as $arg) {
			$result .= ' ' . escapeshellarg($arg);
		}

		return $result;
	}

	private function open($stdin)
	{
		return fopen('data://text/plain;encoding=utf-8,'.$stdin, 'r');
	}

}

?>