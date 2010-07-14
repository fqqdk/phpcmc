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
	/**
	 * @var string the directory over which we iterate
	 */
	private $dir;

	/**
	 * Constructor
	 *
	 * @param string $dir the directory over which we iterate
	 *
	 * @return ClassFileIterator
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
	public function getIterator()
	{
		return new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->dir)
		);
	}
}

?>