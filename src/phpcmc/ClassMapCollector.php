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
	private $listener;

	public function  __construct(PhpCmcListener $listener)
	{
		$this->listener = $listener;
	}

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
					$this->listener->classFound($file, $className);
				}
			}
		}

		$this->listener->searchCompleted();
	}
}

?>