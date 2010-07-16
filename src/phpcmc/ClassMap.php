<?php
/**
 * Holds the ClassMap class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassMap
 */
class ClassMap implements ClassLoader
{
	/**
	 * @var ClassListener for class related events
	 */
	private $listener;

	/**
	 * @var ArrayObject internal class map
	 */
	private $map;

	/**
	 * Constructor
	 *
	 * @param ClassListener $listener for class related events
	 *
	 * @return ClassMap
	 */
	public function __construct(ClassListener $listener)
	{
		$this->listener = $listener;
		$this->map      = new ArrayObject();
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
	 * @param string      $className the class
	 * @param SplFileInfo $file      the file the class has been found in
	 *
	 * @return void
	 */
	public function addClass($className, SplFileInfo $file)
	{
		$path = $file->getPathname();

		if ($this->classIsMapped($className)) {
			$this->listener->duplicate($className, $file, $this->map[$className]);
			return;
		}

		$this->map[$className] = $path;
		$this->listener->classFound($className, $path);
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