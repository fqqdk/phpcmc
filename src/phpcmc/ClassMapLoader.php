<?php
/**
 * Holds the ClassMapLoader class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassMapLoader
 */
class ClassMapLoader implements ClassLoader
{
	/**
	 * @var array internal map of classes to files
	 */
	private $map;

	/**
	 * Constructor
	 *
	 * @param array  $map     the map of classes to files to use internally
	 * @param string $baseDir optional basedir to use with the files
	 *
	 * @return ClassMapLoader
	 */
	public function __construct(array $map, $baseDir='')
	{
		$this->map     = $map;
		$this->baseDir = $baseDir;
	}

	/**
	 * Attempts to load a class
	 *
	 * @param string $className the name of the class to load
	 *
	 * @return boolean
	 */
	public function load($className)
	{
		if (false == isset($this->map[$className])) {
			return false;
		}

		return include_once $this->map[$className];
	}
}

?>