<?php
/**
 * Holds the FileWalker class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of FileWalker
 */
interface FileWalker
{
	/**
	 * Walks a set of files and notifies the FileWalkListener
	 *
	 * @param FileWalkListener $listener the listener
	 *
	 * @return void
	 */
	public function walk(FileWalkListener $listener);
}

?>