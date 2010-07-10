<?php
/**
 * Holds the PhpCmcMainRunner class
 *
 * @author fqqdk <fqqdk@ustream.tv>
 */

/**
 * Description of PhpCmcMainRunner
 */
class PhpCmcMainRunner extends PhpCmcRunner
{
	/**
	 * Runs the application
	 *
	 * @param string $includePath the include path to be set for the application
	 * @param string $theScript   the path to the script
	 *
	 * @return string the output
	 */
	public function run($includePath='.', $theScript='')
	{
		return $this->runMainMethod($includePath);
	}

	protected function assembleArguments()
	{
		return array_merge(array('the_script_self'), parent::assembleArguments());
	}

	/**
	 * Runs the application through the main method
	 *
	 * @return string the output
	 */
	private function runMainMethod($includePath)
	{
		$oldIncludePath = set_include_path($includePath);
		ob_start();
		try {
			PhpCmcApplication::main($this->assembleArguments());
			$result = $this->output = ob_get_contents();
			ob_end_clean();
			set_include_path($oldIncludePath);
		} catch (Exception $ex) {
			ob_end_clean();
			set_include_path($oldIncludePath);
			throw $ex;
		}
		return $result;
	}

	/**
	 * Runs the application in the given directory
	 *
	 * @param string $cmcScript   the script
	 * @param string $dir         the directory
	 * @param string $includePath the include_path to pass to the script
	 *
	 * @return string the output
	 */
	protected function runInDirectory($cmcScript, $dir, $includePath='.')
	{

		return $this->output = $this->runner->runPhpScript($cmcScript, $args, array(), $includePath);
	}
}

?>