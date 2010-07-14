<?php
/**
 * Holds the SummaryFormatter class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of SummaryFormatter
 */
class SummaryFormatter implements OutputFormatter
{
	/**
	 * @var integer the number of classes found
	 */
	private $classCount = 0;

	/**
	 * The header of the output
	 *
	 * @return string
	 */
	public function header()
	{
		return sprintf(
			'phpcmc %s by fqqdk, sebcsaba', PHPCMC_VERSION
		) . PHP_EOL . PHP_EOL;
	}

	/**
	 * One formatted class entry
	 *
	 * @param string $className the class
	 * @param string $file      the file
	 *
	 * @return string
	 */
	public function classEntry($className, $file)
	{
		++$this->classCount;
	}

	/**
	 * The footer of the output
	 *
	 * @return string
	 */
	public function footer()
	{
		return sprintf('found %s classes', $this->classCount) . PHP_EOL;
	}
}

?>