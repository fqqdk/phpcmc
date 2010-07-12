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
	 * @var OutputStream
	 */
	private $stderr;

	public function __construct(OutputStream $stderr)
	{
		$this->stderr = $stderr;
	}

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
		$this->stderr->write($errors);

		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);

		proc_close($proc);

		return false !== strpos($output, 'No syntax errors detected');
	}
}

?>