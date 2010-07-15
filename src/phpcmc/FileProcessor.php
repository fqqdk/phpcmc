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
	private $naming;
	private $map;
	private $listener;

	public function  __construct(PhpCmcNamingConvention $naming, ClassMap $map, PhpCmcListener $listener)
	{
		$this->naming   = $naming;
		$this->map      = $map;
		$this->listener = $listener;
	}

	public function foundFile(SplFileInfo $file)
	{
		$classes = $this->naming->collectPhpClassesFrom($file);

		foreach ($classes as $className) {
			if (isset($this->map[$className])) {
				$this->listener->duplicate($className, $file, $this->map[$className]);
			} else {
				$this->map[$className] = $file->getPathname();
				$this->listener->classFound($className, $file->getPathname());
			}
		}
	}
}

?>