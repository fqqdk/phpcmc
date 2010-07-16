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
		} catch (UnexpectedValueException $ex) {
			throw new PhpCmcException('Cannot walk directory: '. $dir);
		}

		spl_autoload_register(array($classMap, 'load'));
	}
}

?>