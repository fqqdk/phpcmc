<?php
/**
 * Holds the StreamErrorListener class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of StreamErrorListener
 */
class StreamErrorListener implements PhpCmcErrorListener
{
	private $stream;

	/**
	 * Constructor
	 *
	 * @param OutputStream $stream the stream
	 *
	 * @return StreamErrorListener
	 */
	public function  __construct(OutputStream $stream)
	{
		$this->stream = $stream;
	}

	/**
	 * @param string $error error message
	 *
	 * @return void
	 */
	public function error($error)
	{
		$this->stream->write($error . PHP_EOL);
	}
}

?>