<?php
/**
 * Holds the PhpCmcRunner class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcRunner
 */
class PhpCmcRunner
{
	/**
	 * @var PhpScriptRunner
	 */
	private $runner;

	/**
	 * @var Assert
	 */
	private $assert;

	/**
	 * @var string
	 */
	private $output;

	public function __construct(PhpScriptRunner $runner, Assert $assert)
	{
		$this->runner   = $runner;
		$this->assert   = $assert;
	}

	public function runInDirectory($cmcScript, $dir, $includePath='.')
	{
		$this->output = $this->runner->runPhpScript($cmcScript, array($dir), $includePath);
	}

	private function getOutput()
	{
		$result = '';
		foreach (explode(PHP_EOL, $this->output) as $line) {
			$result .= '> ' . $line . PHP_EOL;
		}
		return $result;
	}

	public function outputShows($constraint)
	{
		$this->assert->that(
			$this->output,
			$constraint,
			'Erroneous script output : ' . PHP_EOL . $this->getOutput() . PHP_EOL
		);
	}
}

?>