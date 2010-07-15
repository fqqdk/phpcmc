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
	public function walk(FileWalkListener $listener);
}

?>