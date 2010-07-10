<?php
/**
 * Holds the PhpCmcApplication class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcApplication
 */
class PhpCmcApplication
{
	/**
	 * The library directories for the application
	 *
	 * @return array
	 */
	public static function library()
	{
		return array(dirname(__file__) . '/');
	}

	/**
	 * Main method that runs the application
	 *
	 * @param array $argv the CLI arguments
	 *
	 * @return void
	 * @throws PhpCmcException
	 */
	public static function main(array $argv)
	{
		$cmc = new self;
		$cmc->run($argv);
	}

	/**
	 * Actually runs the application
	 *
	 * @param array $argv the arguments passed to the script
	 *
	 * @return void
	 */
	private function run(array $argv)
	{
		$dir    = $this->getDirectory($argv);
		$format = $this->getFormat($argv);

		$rec = new RecursiveDirectoryIterator($dir);
		$it  = new RecursiveIteratorIterator($rec);

		$classMap = array();


		foreach ($it as $file) {
			if ($this->isPhpClassFile($file)) {
				$className            = $file->getBaseName('.php');
				$classMap[$className] = str_replace('\\', '/', dirname($file->getPathname()));
			}
		}

		if ('assoc' == $format) {
			echo sprintf('<?php return %s; ?'.'>', var_export($classMap, true));
		} else {
			echo sprintf('phpcmc %s by fqqdk, sebcsaba', PHPCMC_VERSION) . PHP_EOL . PHP_EOL;
			echo sprintf('found %s classes', count($classMap));
		}
	}

	/**
	 * The output format argument
	 *
	 * @param array $argv the CLI arguments passed to the script
	 *
	 * @return string
	 */
	private function getFormat(array $argv)
	{
		if (count($argv) > 2) {
			return 'assoc';
		}

		return 'summary';
	}

	/**
	 * The directory argument passed to the script
	 *
	 * @param array $argv the arguments passed to the script
	 *
	 * @return string
	 * @throws PhpCmcException
	 */
	private function getDirectory($argv)
	{
		if (count($argv) < 2) {
			throw new PhpCmcException('the directory argument is mandatory');
		}

		$dirIndex = count($argv) - 1;

		if ('-' === substr($argv[$dirIndex], 0, 1)) {
			throw new PhpCmcException('the directory argument is mandatory');
		}

		return $argv[$dirIndex];
	}

	/**
	 * Determines whether a file contains a PHP class
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return boolean
	 */
	private function isPhpClassFile(SplFileInfo $file)
	{
		return '.php' === strtolower(substr($file->getPathname(), -4));
	}
}

?>