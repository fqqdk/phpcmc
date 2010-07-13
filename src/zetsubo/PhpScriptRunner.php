<?php
/**
 * Holds the PhpScriptRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Facility specially suited to run php commands on the CLI
 */
class PhpScriptRunner implements ShellCommandRunner
{
	/**
	 * Runs a script
	 *
	 * @param string  $shellCommand the command to run
	 * @param string  $stdin        contents that should be piped in as stdin
	 * @param array   $env          environment variables
	 * @param boolean $bypassShell  whether "cmd /c" should be bypassed
	 *                              on windows systems
	 *
	 * @return string the output of the script
	 * @throws ForeignError
	 */
	public function run($shellCommand, $stdin='', array $env=array(), $bypassShell=true)
	{
		if (empty($env)) {
			$env = null;
		}
		$stdinHandle = $this->open($stdin);

		$desc = array(
			0 => $stdinHandle,
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$flags = array('bypass_shell' => $bypassShell);
		$proc  = proc_open($shellCommand, $desc, $pipes, null, $env, $flags);
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

	/**
	 * Opens a file handle for the passed in stdin content
	 *
	 * @param string $stdin the contents of stdin
	 *
	 * @return resource php file handle
	 */
	private function open($stdin)
	{
		return fopen('data://text/plain;encoding=utf-8,'.$stdin, 'r');
	}

	/**
	 * Runs arbitrary PHP code piped to the php executable as stdin
	 *
	 * @param string $scriptContent php code
	 * @param array  $iniVars       ini variables
	 * @param array  $scriptArgs    arguments for the script
	 * @param array  $env           environment variables
	 *
	 * @return string the output of the script
	 */
	public function runPhpScriptFromStdin(
		$scriptContent, array $iniVars=array(), array $scriptArgs=array(),
		array $env=array())
	{
		if (false == empty($env)) {
			$env = array_merge($_ENV, $env);
		}
		return ShellCommandBuilder::newPhp()
			->addIniVars($iniVars)
			->addArgs($scriptArgs)
			->runWith($this, $scriptContent, $env);
	}

	/**
	 * Runs a PHP script file
	 *
	 * @param string $script      the script file
	 * @param array  $args        arguments to pass to the script
	 * @param array  $phpArgs     arguments to pass to the PHP cli
	 * @param string $includePath include_path to pass to the script
	 *
	 * @return string output of the script
	 */
	public function runPhpScript($script, array $args=array(), array $phpArgs=array(), $includePath='.')
	{
		return ShellCommandBuilder::newPhp()
			->addPhpProperty('-f', $script)
			->addIniVars(array('include_path' => $includePath))
			->addPhpArgs($phpArgs)
			->addArgs($args)
			->runWith($this);
	}
}

?>