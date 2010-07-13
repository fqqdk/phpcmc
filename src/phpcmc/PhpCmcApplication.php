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
	 * @var array class map for autoloading
	 */
	private static $classMap = array();

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

	private static function bootstrap()
	{
		if ('@package_version@' == PHPCMC_VERSION) {
			return false;
		}

		self::$classMap = require 'phpcmc.classmap.php';
		spl_autoload_register(array(__class__, 'autoload'));
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
		self::bootstrap();

		$output = new OutputStream();
		$error  = new OutputStream(STDERR);

		$cmc = new self($output, $error);
		$cmc->run($args);
	}

	/**
	 * Autoloads a class
	 *
	 * @param string $className the class name
	 *
	 * @return boolean
	 */
	public static function autoload($className)
	{
		if (false == isset(self::$classMap[$className])) {
			return false;
		}
		include_once self::$classMap[$className];
		return true;
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

		$dir       = $this->getSourceDirectory($opts);
		$naming    = $this->getNamingConvention($opts);
		$formatter = $this->getFormatter($opts, $dir);

		$rec = new RecursiveDirectoryIterator($dir);
		$it  = new RecursiveIteratorIterator($rec);

		$cmc = new ClassMapCollector(new StreamListener($this->output, $this->error, $formatter));

		$classMap = $cmc->collect($it, $naming, $dir);
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
	private function getFormatter(array $opts, $dir)
	{
		switch($opts['format']) {
			case 'assoc'   : return new VarExportFormatter($dir, $opts['prefix']);
			case 'summary' : return new SummaryFormatter;
			default        : //fall-through
		}

		throw new PhpCmcException(sprintf('Invalid formatter: %s', $opts['format']));
	}
}

?>