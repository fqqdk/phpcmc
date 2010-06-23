<?php
/**
 * Holds the ShellCommandRunner class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Implementors are able to run shell commands
 */
interface ShellCommandRunner
{
	/**
	 * Runs a shell command
	 *
	 * @param string  $shellCommand the shell command
	 * @param string  $stdin        contents that should be passed
	 *                              to the command's stdin
	 * @param array   $env          environment variables
	 * @param boolean $bypassShell  whether 'cmd /c' should be bypassed
	 *                              on windows systems
	 *
	 * @return string the output of the command
	 */
	public function run($shellCommand, $stdin='', array $env=array(), $bypassShell = true);
}

?>