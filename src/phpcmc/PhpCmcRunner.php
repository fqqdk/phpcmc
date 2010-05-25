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
	 * @var FileSystemDriver
	 */
	private $fsDriver;

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

	public function __construct(
		FileSystemDriver $fsDriver, PhpScriptRunner $runner, Assert $assert
	)
	{
		$this->fsDriver = $fsDriver;
		$this->runner   = $runner;
		$this->assert   = $assert;
	}

	public function runInDirectory($dir)
	{
		$this->output = $this->runner->run('php', array(
			BASE_DIR . 'src/phpcmc.php',
			$this->fsDriver->absolute($dir),
		), '');
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