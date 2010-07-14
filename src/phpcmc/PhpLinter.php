<?php
/**
 * Holds the PhpLinter class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpLinter
 */
class PhpLinter
{
	/**
	 * @var OutputStream the error stream to report parse errors to
	 */
	private $listener;

	/**
	 * Constructor
	 *
	 * @param OutputStream $listener listener to report errors to
	 *
	 * @return PhpLinter
	 */
	public function __construct(PhpCmcListener $listener)
	{
		$this->listener = $listener;
	}

	/**
	 * Checks the PHP syntax of the file
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return boolean false if the file contains syntax errors
	 * @throws PhpCmcException if 'php -l' can't be run on the file
	 */
	public function checkSyntax(SplFileInfo $file)
	{
		$shellCommand = 'php -l ' .escapeshellarg($file->getPathname());

		$desc = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$proc  = proc_open($shellCommand, $desc, $pipes);
		if (false === $proc) {
			throw new PhpCmcException('Cannot lint php file, -nparse wont work');
		}

		$output = stream_get_contents($pipes[1]);
		$errors = stream_get_contents($pipes[2]);
		$this->listener->error($errors);

		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);

		proc_close($proc);

		return false !== strpos($output, 'No syntax errors detected');
	}
}

?>