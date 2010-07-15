<?php
/**
 * Holds the ClassMap class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassMap
 */
class ClassMap
{
	/**
	 * Constructor
	 *
	 * @return ClassMap
	 */
	public function __construct()
	{
		$this->map = new ArrayObject();
	}

	/**
	 * Checks that a class is already mapped
	 *
	 * @param string $className the class name
	 *
	 * @return boolean
	 */
	public function classIsMapped($className)
	{
		return isset($this->map[$className]);
	}

	/**
	 * Adds a class to the map
	 *
	 * @param string         $className the class
	 * @param SplFileInfo    $file      the file the class has been found in
	 * @param PhpCmcListener $listener  the listener to report
	 *
	 * @return void
	 */
	public function addClass($className, SplFileInfo $file, PhpCmcListener $listener)
	{
		$path = $file->getPathname();

		if ($this->classIsMapped($className)) {
			$listener->duplicate($className, $file, $this->map[$className]);
			return;
		}

		$this->map[$className] = $path;
		$listener->classFound($className, $path);
	}

	/**
	 * Returns an array copy of the internal map
	 *
	 * @return array
	 */
	public function getArrayCopy()
	{
		return $this->map->getArrayCopy();
	}
}

?>