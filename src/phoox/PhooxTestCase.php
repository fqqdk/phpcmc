<?php
/**
 * Holds the PhooxTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Common base class for test cases that want to utilize phoox functionality
 */
class PhooxTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * Gets the full path of the file that contains the class
	 *
	 * @param string $className the name of the class
	 *
	 * @return string
	 */
	protected function getFileOfClass($className)
	{
		$rc = new ReflectionClass($className);
		return $rc->getFileName();
	}

}

?>