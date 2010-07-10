<?php
/**
 * Holds the PhpCmcOptsParser class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcOptsParser
 */
class PhpCmcOptsParser
{
	private $options = array(
		'f' => 'format',
		'n' => 'naming',
	);

	private $defaults = array(
		'format' => 'summary',
		'naming' => 'filebasename',
	);

	private $validate = array(
		'format' => array('summary', 'assoc'),
		'naming' => array('filebasename', 'parse'),
	);

	/**
	 * Parses the CLI options passed
	 *
	 * @param array $argv CLI options
	 *
	 * @return array
	 */
	public function parse(array $argv)
	{
		if (count($argv) < 2) {
			throw new PhpCmcException('Directory argument is mandatory');
		}

		$result = array(
			'script' => array_shift($argv),
			'dir'    => array_pop($argv),
		);

		$shortOpts = array();

		foreach ($argv as $argument) {
			$option      = substr($argument, 1, 1);
			$optionValue = substr($argument, 2);

			$shortOpts[$option] = $optionValue;
		}

		foreach ($this->options as $short => $long) {
			if (isset($shortOpts[$short])) {
				$result[$long] = $shortOpts[$short];
			} else {
				$result[$long] = $this->defaults[$long];
			}
		}

		return $result;
	}
}

?>