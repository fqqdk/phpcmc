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
		$classMap  = new ClassMap($listener);
		$naming    = new ParsingConvention(new PhpLinter($listener));
		$collector = new ClassMapCollector($naming, $classMap);

		$collector->collect(new RecursiveDirectoryWalker($dir), $listener);

		spl_autoload_register(array($classMap, 'load'));
	}
}

?>