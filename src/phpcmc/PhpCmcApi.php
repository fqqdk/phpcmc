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
	public static function registerLoaderOverSourceDir($dir)
	{
		require_once dirname(__file__).'/PhpCmcApplication.php';
		PhpCmcApplication::bootstrap();

		$listener  = new ApiListener();
		$collector = new ClassMapCollector($listener);

		$naming = new ParsingConvention(new PhpLinter($listener));

		try {
			$classMap  = $collector->collect(new ClassFileIterator($dir), $naming, $dir);
		} catch (UnexpectedValueException $ex) {
			throw new PhpCmcException('Cannot walk directory: '. $dir);
		}

		spl_autoload_register(array(new self($classMap), 'loadClass'));
	}

	public function __construct(array $classMap)
	{
		$this->classMap = $classMap;
	}

	public function loadClass($className)
	{
		if (false == isset($this->classMap[$className])) {
			return false;
		}

		include_once $this->classMap[$className];
	}
}

?>