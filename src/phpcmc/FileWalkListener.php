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
	public function foundFile(SplFileInfo $file);
}

?>