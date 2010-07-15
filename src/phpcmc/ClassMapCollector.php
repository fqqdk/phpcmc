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
	 * @param FileWalker             $walker the file walker
	 * @param PhpCmcNamingConvention $naming the naming convention used to find classes
	 *                                       in the files
	 * @param string                 $dir    the directory
	 *
	 * @return array the raw classmap
	 */
	public function collect(FileWalker $walker, PhpCmcNamingConvention $naming, $dir)
	{
		$map = new ClassMap();

		$this->listener->searchStarted();

		$walker->walk(new FileProcessor($naming, $map, $this->listener));

		$this->listener->searchCompleted();

		return $map->getArrayCopy();
	}
}

?>