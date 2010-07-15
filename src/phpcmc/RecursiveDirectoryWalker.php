<?php
/**
 * Holds the RecursiveDirectoryWalker class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of RecursiveDirectoryWalker
 */
class RecursiveDirectoryWalker implements FileWalker
{
	/**
	 * @var string the directory over which we iterate
	 */
	private $dir;

	/**
	 * Constructor
	 *
	 * @param string $dir the directory over which we iterate
	 *
	 * @return RecursiveDirectoryWalker
	 */
	public function __construct($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * Delegated iterator
	 *
	 * @return Iterator
	 */
	private function getIterator()
	{
		return new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->dir),
			RecursiveIteratorIterator::SELF_FIRST
		);
	}

	public function walk(FileWalkListener $listener)
	{
		foreach ($this->getIterator() as $file) {
			$fileName = $file->getFileName();
			if ('.' == $fileName || '..' == $fileName) {
				continue;
			}

			$listener->foundFile($file);
		}
	}
}

?>