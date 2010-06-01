<?php
/**
 * Holds the PhpScriptRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpScriptRunner
 */
class PhpScriptRunner implements ShellCommandRunner
{
	public function run($shellCommand, $stdin='', array $env=array(), $bypassShell = true)
	{
//	if (false !== strpos($shellCommand, 'php'))
//	print $shellCommand . PHP_EOL;
		if (empty($env)) {
			$env = null;
		}
		$stdinHandle = $this->open($stdin);

		$desc = array(
			0 => $stdinHandle,
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$proc = proc_open($shellCommand, $desc, $pipes, null, $env, array('bypass_shell' => $bypassShell));
		if (false === $proc) {
			trigger_error('cannot execute ' . $shellCommand);
		}

		$result = stream_get_contents($pipes[1]);
		$errors = stream_get_contents($pipes[2]);

		if (false == empty($errors)) {
			throw new ForeignError($result, $errors);
		}

		fclose($pipes[1]);
		fclose($pipes[2]);
		fclose($stdinHandle);
		proc_close($proc);

		return $result;
	}

	private function open($stdin)
	{
		return fopen('data://text/plain;encoding=utf-8,'.$stdin, 'r');
	}

	public function runPhpScriptFromStdin($scriptContent, array $iniVars=array(), array $scriptArgs=array(), array $env=array())
	{
	    if (false == empty($env)) {
			$env = array_merge($_ENV, $env);
		}
		return ShellCommandBuilder::newPhp()
			->addIniVars($iniVars)
			->addArgs($scriptArgs)
			->runWith($this, $scriptContent, $env);
	}

	public function runPhpScript($script, array $args=array(), $includePath)
	{
		return ShellCommandBuilder::newPhp()
			->addPhpProperty('-f', $script)
			->addIniVars(array('include_path' => $includePath))
			->addPhpArgs($args)
			->runWith($this);
	}
}

?>