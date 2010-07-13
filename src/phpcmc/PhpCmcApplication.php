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
		error_reporting(E_ALL);
		if (false == class_exists('Bootstrapper', false)) {
			require_once 'angst/Bootstrapper.php';
		}
		Bootstrapper::bootstrap(self::library());

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

		$cmc = new ClassMapGenerator(new StreamErrorListener($this->error));

		$classMap = $cmc->generateClassMap($it, $naming, $dir);

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
}

?>