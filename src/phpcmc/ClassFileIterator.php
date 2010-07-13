<?php
/**
 * Holds the ClassFileIterator class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassFileIterator
 */
class ClassFileIterator implements IteratorAggregate
{
	private $dir;

	public function __construct($dir)
	{
		$this->dir = $dir;
	}

	public function getIterator()
	{
		return new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->dir)
		);
	}
}

?>