<?php
/**
 * Holds the PhpCmcIsolatedRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcIsolatedRunner
 */
class PhpCmcIsolatedRunner implements PhpCmcRunner
{
	/**
	 * @var PhpScriptRunner script runner
	 */
	private $runner;

	/**
	 * @var string the script to run
	 */
	private $script;

	/**
	 * Constructor
	 *
	 * @param PhpScriptRunner $runner the script runner
	 * @param string          $script the application script
	 *
	 * @return PhpCmcIsolatedRunner
	 */
	public function __construct($runner, $script)
	{
		$this->runner = $runner;
		$this->script = $script;
	}

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
		try {
			$output = $this->runner->runPhpScriptWithPrepend(
				$this->script, $args, array(), $includePath,
				BOOTSTRAP_FILE
			);
			return array($output, '');
		} catch (ForeignError $ex) {
			return array($ex->getOutput(), $ex->getError());
		}
	}
}

?>