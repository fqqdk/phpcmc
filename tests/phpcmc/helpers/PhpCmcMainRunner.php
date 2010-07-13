<?php
/**
 * Holds the PhpCmcMainRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcMainRunner
 */
class PhpCmcMainRunner implements PhpCmcRunner
{
	/**
	 * Runs the application
	 *
	 * @param string $includePath the include path to be set for the application
	 * @param array  $args        arguments to pass to the application
	 *
	 * @return array
	 */
	public function run($includePath, array $args)
	{
		$args = array_merge(array('the_script_self'), $args);
		$oldIncludePath = set_include_path($includePath);
		try {
			$outputStream = new SpyStream();
			$errorStream  = new SpyStream();
			$app = new PhpCmcApplication($outputStream, $errorStream);
			$app->run($args);
			set_include_path($oldIncludePath);
		} catch (Exception $ex) {
			set_include_path($oldIncludePath);
			throw $ex;
		}

		return array($outputStream->getContents(), $errorStream->getContents());
	}
}

?>