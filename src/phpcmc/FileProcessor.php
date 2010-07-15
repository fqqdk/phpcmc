<?php
/**
 * Holds the FileProcessor class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of FileProcessor
 */
class FileProcessor implements FileWalkListener
{
	/**
	 * @var PhpCmcNamingConvention naming convention
	 */
	private $naming;

	/**
	 * @var ClassMap the class map
	 */
	private $map;

	/**
	 * @var PhpCmcListener the listener
	 */
	private $listener;

	/**
	 * Constructor
	 *
	 * @param PhpCmcNamingConvention $naming   the naming convention
	 * @param ClassMap               $map      the class map
	 * @param PhpCmcListener         $listener the listener
	 */
	public function __construct(
		PhpCmcNamingConvention $naming, ClassMap $map, PhpCmcListener $listener
	)
	{
		$this->naming   = $naming;
		$this->map      = $map;
		$this->listener = $listener;
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
			$this->map->addClass($className, $file, $this->listener);
		}
	}
}

?>