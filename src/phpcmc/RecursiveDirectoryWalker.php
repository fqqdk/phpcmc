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
		$h     = opendir($this->dir);
		$files = array();
		$dirs  = array();
		while ($file = readdir($h)) {
			if (is_dir($this->dir . DIRECTORY_SEPARATOR . $file)) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				$dirs []= $file;
			} else {
				$files []= $file;
			}
		}

		closedir($h);
		sort($dirs);
		sort($files);

		foreach ($dirs as $dir) {
			$full = $this->dir . DIRECTORY_SEPARATOR . $dir;
			$listener->foundFile(new SplFileInfo($full));
			$innerWalker = new self($full);
			$innerWalker->walk($listener);
		}

		foreach ($files as $file) {
			$listener->foundFile(new SplFileInfo($file));
		}
	}
}

?>