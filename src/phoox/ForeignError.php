<?php
/**
 * Holds the ForeignError class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
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
	 * @return string the output of the process
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @return string the error output of the process
	 */
	public function getError()
	{
		return $this->error;
	}
}

?>