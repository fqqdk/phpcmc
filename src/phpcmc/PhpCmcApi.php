<?php
/**
 * Holds the PhpCmcApi class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcApi
 */
class PhpCmcApi
{
	/**
	 * Registers a magical autoloader that is able to find classes in the given
	 * source folder
	 *
	 * @param string $dir the directory
	 *
	 * @return void
	 * @throws PhpCmcException
	 */
	public static function registerLoaderFor($dir)
	{
		require_once dirname(__file__).'/PhpCmcApplication.php';
		PhpCmcApplication::bootstrap();

		$listener  = new ApiListener();
		$classMap  = new ClassMap();
		$naming    = new ParsingConvention(new PhpLinter($listener));
		$collector = new ClassMapCollector($listener, $naming, $classMap);

		try {
			$collector->collect(new RecursiveDirectoryWalker($dir));
			$rawClassMap = $classMap->getArrayCopy();
		} catch (UnexpectedValueException $ex) {
			throw new PhpCmcException('Cannot walk directory: '. $dir);
		}

		spl_autoload_register(array(new self($rawClassMap), 'loadClass'));
	}

	/**
	 * Constructor
	 *
	 * @param array $classMap the classmap
	 *
	 * @todo extract to a separate classloader
	 *
	 * @return PhpCmcApi
	 */
	public function __construct(array $classMap)
	{
		$this->classMap = $classMap;
	}

	/**
	 * Loads a class
	 *
	 * @param string $className the name of the class to load
	 *
	 * @return boolean
	 */
	public function loadClass($className)
	{
		if (false == isset($this->classMap[$className])) {
			return false;
		}

		return include_once $this->classMap[$className];
	}
}

?>