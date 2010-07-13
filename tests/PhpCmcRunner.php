<?php
/**
 * Holds the PhpCmcRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcRunner
 */
interface PhpCmcRunner
{
	/**
	 * Runs the application
	 *
	 * @param string $includePath the include path to be set for the application
	 * @param array  $args        arguments to pass to the application
	 *
	 * @return array
	 */
	public function run($includePath, array $args);
}

?>