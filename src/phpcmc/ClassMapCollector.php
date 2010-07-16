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
	 * @param PhpCmcListener         $listener the listener
	 * @param PhpCmcNamingConvention $naming   the naming convention
	 * @param ClassMap               $map      the classmap to collect the classes into
	 *
	 * @return ClassMapCollector
	 */
	public function __construct(
		PhpCmcListener $listener, PhpCmcNamingConvention $naming, ClassMap $map
	)
	{
		$this->listener = $listener;
		$this->naming   = $naming;
		$this->map      = $map;
	}

	/**
	 * Traverses a file iterator and reports found classes and errors to listener
	 *
	 * @param FileWalker $walker the file walker
	 * 
	 * @return array the raw classmap
	 */
	public function collect(FileWalker $walker)
	{
		try {
			$this->listener->searchStarted();

			$processor = new FileProcessor($this->naming, $this->map, $this->listener);
			$walker->walk($processor);

			$this->listener->searchCompleted();
		} catch(UnexpectedValueException $ex) {
			$this->listener->error('Cant walk directory: '. $dir);
		}
	}
}

?>