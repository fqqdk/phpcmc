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
	 * @param array $args the CLI arguments
	 *
	 * @return void
	 * @throws PhpCmcException
	 */
	public static function main(array $args)
	{
		$cmc = new self;
		$cmc->run($args);
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
		$optsParser = new PhpCmcOptsParser();
		$opts       = $optsParser->parse($argv);

		$dir    = $this->getSourceDirectory($opts);
		$format = $this->getFormat($opts);
		$naming = $this->getNamingConvention($opts);

		$rec = new RecursiveDirectoryIterator($dir);
		$it  = new RecursiveIteratorIterator($rec);

		$classMap = array();

		foreach ($it as $file) {
			$classes = $naming->collectPhpClassesFrom($file);

			foreach ($classes as $className) {
				$classMap[$className] = $this->getClassDirectory($dir, $file);
			}
		}

		if ('assoc' == $format) {
			echo sprintf('<?php return %s; ?'.'>', var_export($classMap, true));
		} else {
			echo sprintf('phpcmc %s by fqqdk, sebcsaba', PHPCMC_VERSION) . PHP_EOL . PHP_EOL;
			echo sprintf('found %s classes', count($classMap)) . PHP_EOL;
		}
	}


	/**
	 * The directory argument passed to the script
	 *
	 * @param array $opts the arguments passed to the script
	 *
	 * @return string
	 * @throws PhpCmcException
	 */
	private function getSourceDirectory($opts)
	{
		return $opts['dir'];
	}

	/**
	 * Retrie
	 *
	 * @param array $opts
	 * @return <type>
	 */
	private function getNamingConvention(array $opts)
	{
		switch($opts['naming']) {
			case 'filebasename': return new FileBaseNameConvention();
			case 'parse'       : return new ParsingConvention();
			default            : //fall-through
		}

		throw new PhpCmcException('Invalid naming convention');
	}

	/**
	 * The output format argument
	 *
	 * @param array $opts the CLI arguments passed to the script
	 *
	 * @return string
	 */
	private function getFormat(array $opts)
	{
		return $opts['format'];
	}

	/**
	 * Callculates the directory string that should be displayed for a class entry
	 *
	 * @param string      $dir  the base source directory
	 * @param SplFileInfo $file the class file
	 *
	 * @return string
	 */
	private function getClassDirectory($dir, SplFileInfo $file)
	{
		$result = $file->getPathname();
		$result = str_replace('\\', '/', $result);
		$result = str_replace($dir, '', $result);

		return $result;
	}
}

?>