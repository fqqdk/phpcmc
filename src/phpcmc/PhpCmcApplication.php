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
	 * Bootstraps the application:
	 * - registers autoloaders
	 *
	 * @return void
	 */
	public static function bootstrap()
	{
		if (false == defined('PHPCMC_VERSION') || '@package_version@' == PHPCMC_VERSION) {
			return false;
		}

		require_once dirname(__file__).'/ClassLoader.php';
		require_once dirname(__file__).'/ClassMapLoader.php';

		$loader = new ClassMapLoader(require 'phpcmc.classmap.php');

		spl_autoload_register(array($loader, 'load'));
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
		try {
			$cmc->run($args);
		} catch (PhpCmcException $ex) {
			$error->write($ex->getMessage() . PHP_EOL);
		}
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
		$formatter = $this->getFormatter($opts, $dir);
		$listener  = new StreamListener($this->output, $this->error, $formatter);
		$linter    = new PhpLinter($listener);
		$naming    = $this->getNamingConvention($opts, $linter);
		$classMap  = new ClassMap($listener);

		$collector = new ClassMapCollector($naming, $classMap);

		$collector->collect(new RecursiveDirectoryWalker($dir), $listener);
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
	 * @param array     $opts   options parsed from the command line
	 * @param PhpLinter $linter linter
	 *
	 * @return PhpCmcNamingConvention
	 * @throws PhpCmcException
	 */
	private function getNamingConvention(array $opts, PhpLinter $linter)
	{
		switch($opts['naming']) {
			case 'filebasename': return new FileBaseNameConvention();
			case 'parse'       : return new ParsingConvention($linter);
			default            : //fall-through
		}

		throw new PhpCmcException('Invalid naming convention');
	}

	/**
	 * The output format argument
	 *
	 * @param array  $opts the CLI arguments passed to the script
	 * @param string $dir  the directory
	 *
	 * @return string
	 * @throws PhpCmcException
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