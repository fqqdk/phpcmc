<?php
/**
 * Holds the StreamErrorListener class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of StreamErrorListener
 */
class StreamListener implements PhpCmcListener
{
	/**
	 * @var OutputStream output stream
	 */
	private $error;

	/**
	 * @var OutputStream error stream
	 */
	private $output;

	/**
	 * @var OutputFormatter formatter
	 */
	private $formatter;

	/**
	 * Constructor
	 *
	 * @param OutputStream $error the stream
	 *
	 * @return StreamListener
	 */
	public function  __construct(OutputStream $output, OutputStream $error, OutputFormatter $formatter)
	{
		$this->output    = $output;
		$this->error     = $error;
		$this->formatter = $formatter;
	}

	/**
	 * @param string $error error message
	 *
	 * @return void
	 */
	public function error($error)
	{
		$this->error->write($error . PHP_EOL);
	}

	public function searchStarted()
	{
		$this->output->write($this->formatter->header());
	}

	public function duplicate($className, $file, $originalFile)
	{
		$message = sprintf(
			'Duplicate class %s in %s, first defined in %s' . PHP_EOL,
			$className, $file, $originalFile
		);

		$this->error->write($message);
	}

	public function searchCompleted()
	{
		$this->output->write($this->formatter->footer());
	}

	public function classFound($file, $className)
	{
		$this->output->write($this->formatter->classEntry($file, $className));
	}
}

?>