<?php
/**
 * Holds the ClassMapCollector class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassMapCollector
 */
class ClassMapCollector
{
	/**
	 * @var PhpCmcListener the listener who will be updated
	 */
	private $listener;

	/**
	 * Constructor
	 *
	 * @param PhpCmcListener $listener the listener
	 *
	 * @return ClassMapCollector
	 */
	public function __construct(PhpCmcListener $listener)
	{
		$this->listener = $listener;
	}

	/**
	 * Traverses a file iterator and reports found classes and errors to listener
	 *
	 * @param Traversable            $it     the file iterator
	 * @param PhpCmcNamingConvention $naming the naming convention used to find classes
	 *                                       in the files
	 * @param string                 $dir    the directory
	 *
	 * @return array the raw classmap
	 */
	public function collect(Traversable $it, PhpCmcNamingConvention $naming, $dir)
	{
		$allClasses = array();

		$this->listener->searchStarted();

		foreach ($it as $file) {
			$classes = $naming->collectPhpClassesFrom($file);

			foreach ($classes as $className) {
				if (isset($allClasses[$className])) {
					$this->listener->duplicate($className, $file, $allClasses[$className]);
				} else {
					$allClasses[$className] = $file->getPathname();
					$this->listener->classFound($className, $file->getPathname());
				}
			}
		}

		$this->listener->searchCompleted();

		return $allClasses;
	}
}

?>