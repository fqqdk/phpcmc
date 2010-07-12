<?php
/**
 * Holds the PhpCmcMainRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcMainRunner
 */
class PhpCmcMainRunner extends PhpCmcRunner
{
	private $app;

	public function __construct(PhpCmcApplication $app, Assert $assert)
	{
		parent::__construct('', $assert);
		$this->app = $app;
	}

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

	/**
	 * Assembles the arguments that will be passed to the application
	 *
	 * @return array
	 */
	protected function assembleArguments()
	{
		return array_merge(array('the_script_self'), parent::assembleArguments());
	}

	/**
	 * Runs the application through the main method
	 *
	 * @param string $includePath the include path to set for the application's run
	 *
	 * @return string the output
	 * @throws Exception
	 */
	private function runMainMethod($includePath)
	{
		$oldIncludePath = set_include_path($includePath);
		ob_start();
		try {
			$this->app->run($this->assembleArguments());
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
}

?>