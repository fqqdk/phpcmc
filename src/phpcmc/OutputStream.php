<?php
/**
 * Holds the OutputStream class
 *
 * @author fqqdk <fqqdk@ustream.tv>
 */

/**
 * Description of OutputStream
 */
class OutputStream
{
	/**
	 * @var resource stream an fwritable file handle resource
	 */
	private $stream;

	/**
	 * Constructor
	 *
	 * @param resource $stream an fwritable file handle resource
	 * 
	 * @return OutputStream
	 */
	public function  __construct($stream=null)
	{
		$this->stream = $stream;
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
		if (null === $this->stream) {
			echo $string;
			return;
		}

		fwrite($this->stream, $string);
	}
}

?>