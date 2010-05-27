<?php
/**
 * Holds the ShellCommandRunner class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of ShellCommandRunner
 */
interface ShellCommandRunner
{
	public function run($shellCommand, $stdin='', array $env=array(), $bypassShell = true);
}

?>