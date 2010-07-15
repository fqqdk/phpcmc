<?php
/**
 * Holds the FileWalkListener class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of FileWalkListener
 */
interface FileWalkListener
{
	/**
	 * This event is fired when a FileWalker finds a file
	 *
	 * @param SplFileInfo $file the found file
	 *
	 * @return void
	 */
	public function foundFile(SplFileInfo $file);
}

?>