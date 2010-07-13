<?php
/**
 * Holds the SpyStream class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of SpyStream
 */
class SpyStream extends OutputStream
{
	/**
	 * @var string stream contents
	 */
	private $stream;

	/**
	 * Constructor
	 *
	 * @return SpyStream
	 */
	public function __construct()
	{
	}

	/**
	 * Writes string to stream
	 *
	 * @param string $string text to write to the output
	 *
	 * @return void
	 */
	public function write($string)
	{
		$this->stream .= $string;
	}

	/**
	 * The stream contents
	 *
	 * @return string
	 */
	public function getContents()
	{
		return $this->stream;
	}
}

?>