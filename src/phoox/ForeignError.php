<?php
/**
 * Holds the ForeignError class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * ForeignError is a special exception used by the phoox framework to indicate
 * that an error is occured in a separate process
 */
class ForeignError extends Exception
{
	/**
	 * @var string the output of the process
	 */
	private $output;

	/**
	 * @var string the error output of the process
	 */
	private $error;

	/**
	 * Constructor
	 *
	 * @param string $output  the output of the process
	 * @param string $error   the error output of the process
	 * @param string $message additional optional failure message
	 *
	 * @return ForeignError
	 */
	public function __construct($output, $error, $message='')
	{
		parent::__construct(sprintf(
			$message . PHP_EOL .
			'OUTPUT was : %sERROR  was : %s',
			PHP_EOL . $output . PHP_EOL,
			PHP_EOL . $error  . PHP_EOL
		));

		$this->output = $output;
		$this->error  = $error;
	}

	/**
	 * The output of the process
	 *
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * The error output of the process
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}
}

?>