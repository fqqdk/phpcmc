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
	 * @param OutputStream    $output    output stream
	 * @param OutputStream    $error     error stream
	 * @param OutputFormatter $formatter output formatter
	 *
	 * @return StreamListener
	 */
	public function __construct(OutputStream $output, OutputStream $error, OutputFormatter $formatter)
	{
		$this->output    = $output;
		$this->error     = $error;
		$this->formatter = $formatter;
	}

	/**
	 * This event is fired when an error occurs during class map collection
	 *
	 * @param string $error the error message
	 *
	 * @return void
	 */
	public function error($error)
	{
		$this->error->write($error . PHP_EOL);
	}

	/**
	 * This event is fired when the collector finds a class
	 *
	 * @param string $className the name of the found class
	 * @param string $file      the path of the file in which the class has been found
	 *
	 * @return void
	 */
	public function classFound($className, $file)
	{
		$this->output->write($this->formatter->classEntry($className, $file));
	}

	/**
	 * This event is fired when the search is started
	 *
	 * @return void
	 */
	public function searchStarted()
	{
		$this->output->write($this->formatter->header());
	}

	/**
	 * This event is fired when a duplicate class is found
	 *
	 * @param string $className    the duplicate class
	 * @param string $file         the file in which the duplicate class has been found
	 * @param string $originalFile the file in which the class has been found the first time
	 *
	 * @return void
	 */
	public function duplicate($className, $file, $originalFile)
	{
		$message = sprintf(
			'Duplicate class %s in %s, first defined in %s' . PHP_EOL,
			$className, $file, $originalFile
		);

		$this->error->write($message);
	}

	/**
	 * This event is fired when the search is completed
	 *
	 * @return void
	 */
	public function searchCompleted()
	{
		$this->output->write($this->formatter->footer());
	}
}

?>