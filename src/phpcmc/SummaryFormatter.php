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
	private $classCount = 0;

	public function header()
	{
		return sprintf(
			'phpcmc %s by fqqdk, sebcsaba', PHPCMC_VERSION
		) . PHP_EOL . PHP_EOL;
	}

	public function classEntry($file, $className)
	{
		++$this->classCount;
	}

	public function footer()
	{
		return sprintf('found %s classes', $this->classCount) . PHP_EOL;
	}
}

?>