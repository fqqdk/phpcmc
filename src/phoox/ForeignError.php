<?php
/**
 * Holds the ForeignError class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of ForeignError
 */
class ForeignError extends Exception
{
	private $output;
	private $error;

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

	public function getOutput()
	{
		return $this->output;
	}

	public function getError()
	{
		return $this->error;
	}
}

?>