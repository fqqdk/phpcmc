<?php
/**
 * Holds the ForeignFailure class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of ForeignFailure
 */
class ForeignFailure extends PHPUnit_Framework_TestFailure
{
	public function getExceptionAsString()
	{
		if ($this->thrownException instanceof ForeignError) {
			return $this->thrownException->getMessage();
		}

		return parent::getExceptionAsString();
	}
}

?>