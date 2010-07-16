<?php
/**
 * Holds the ClassMapCollector class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassMapCollector
 */
class ClassMapCollector implements FileWalkListener
{
	/**
	 * @var PhpCmcNamingConvention the naming convention
	 */
	private $naming;

	/**
	 * @var ClassMap the class map to collect files into
	 */
	private $map;

	/**
	 * Constructor
	 *
	 * @param PhpCmcNamingConvention $naming the naming convention
	 * @param ClassMap               $map    the classmap to collect the classes into
	 *
	 * @return ClassMapCollector
	 */
	public function __construct(PhpCmcNamingConvention $naming, ClassMap $map)
	{
		$this->naming   = $naming;
		$this->map      = $map;
	}

	/**
	 * Traverses a file iterator and reports found classes and errors to listener
	 *
	 * @param FileWalker      $walker   the file walker
	 * @param CollectListener $listener the listener for the collection events
	 * 
	 * @return array the raw classmap
	 */
	public function collect(FileWalker $walker, CollectListener $listener)
	{
		try {
			$listener->searchStarted();

			$walker->walk($this);

			$listener->searchCompleted();
		} catch(UnexpectedValueException $ex) {
			$listener->error('Cant walk directory: '. $dir);
		}
	}

	/**
	 * This event is fired when a FileWalker finds a file
	 *
	 * @param SplFileInfo $file the found file
	 *
	 * @return void
	 */
	public function foundFile(SplFileInfo $file)
	{
		$classes = $this->naming->collectPhpClassesFrom($file);

		foreach ($classes as $className) {
			$this->map->addClass($className, $file);
		}
	}
}

?>