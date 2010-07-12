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
	 * @var OutputStream output stream
	 */
	private $output;

	/**
	 * @var OutputStream output stream
	 */
	private $error;

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
		$output = new OutputStream();
		$error  = new OutputStream(STDERR);

		$cmc = new self($output, $error);
		$cmc->run($args);
	}

	/**
	 * Constructor
	 *
	 * @param OutputStream $output output stream
	 * @param OutputStream $error  error stream
	 *
	 * @return PhpCmcApplication
	 */
	public function __construct(OutputStream $output, OutputStream $error)
	{
		$this->output = $output;
		$this->error  = $error;
	}

	/**
	 * Actually runs the application
	 *
	 * @param array $argv the arguments passed to the script
	 *
	 * @return void
	 */
	public function run(array $argv)
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
				$location = $this->getClassDirectory($dir, $file);
				if (isset($classMap[$className])) {
					$message = sprintf(
						'Duplicate class %s in %s, first defined in %s',
						$className, $classMap[$className], $location
					);
					$this->error->write($message . PHP_EOL);
				}
				$classMap[$className] = $location;
			}
		}

		if ('assoc' == $format) {
			$this->output->write(sprintf('<?php return %s; ?'.'>', var_export($classMap, true)));
		} else {
			$this->output->write($this->getSummaryHeader());
			$this->output->write($this->getSummaryFooter(count($classMap)));
		}
	}

	/**
	 * The summary header
	 *
	 * @return string
	 */
	private function getSummaryHeader()
	{
		return sprintf('phpcmc %s by fqqdk, sebcsaba', PHPCMC_VERSION) . PHP_EOL . PHP_EOL;
	}

	/**
	 * The summary footer
	 *
	 * @param integer $classCount number of classes found
	 *
	 * @return string
	 */
	private function getSummaryFooter($classCount)
	{
		return sprintf('found %s classes', $classCount) . PHP_EOL;
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
	 * Retrieves the naming convention
	 *
	 * @param array $opts options parsed from the command line
	 *
	 * @return PhpCmcNamingConvention
	 * @throws PhpCmcException
	 */
	private function getNamingConvention(array $opts)
	{
		switch($opts['naming']) {
			case 'filebasename': return new FileBaseNameConvention();
			case 'parse'       : return new ParsingConvention(new PhpLinter($this->error));
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
		$result = str_replace($dir, '', $result);
		$result = str_replace('\\', '/', $result);

		return $result;
	}
}

?>