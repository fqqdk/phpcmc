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
	 */
	public static function main(array $argv)
	{
		echo sprintf('phpcmc %s by fqqdk, sebcsaba', PHPCMC_VERSION) . PHP_EOL . PHP_EOL;

		if (false == isset($argv[1])) {
			trigger_error('the directory argument is mandatory');
		}

		$dir = $argv[1];

		$rec = new RecursiveDirectoryIterator($dir);
		$it  = new RecursiveIteratorIterator($rec);

		$classMap = array();

		foreach ($it as $file) {
			if (self::isPhpClassFile($file)) {
				$className = $file->getBaseName('.php');
				$classMap[$className] = str_replace('\\', '/', dirname($file->getPathname()));
			}
		}

		foreach ($classMap as $className => $path) {
			echo $className . ' ' . $path . PHP_EOL;
		}
	}

	/**
	 * Determines whether a file contains a PHP class
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return boolean
	 */
	private static function isPhpClassFile(SplFileInfo $file)
	{
		return '.php' === strtolower(substr($file->getPathname(), -4));
	}
}

?>