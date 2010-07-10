<?php
/**
 * Holds the FileBaseNameConvention class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of FileBaseNameConvention
 */
class FileBaseNameConvention implements PhpCmcNamingConvention
{
	/**
	 * Collects the PHP classes from a file
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return array
	 */
	public function collectPhpClassesFrom(SplFileInfo $file)
	{
		if (false == $this->isPhpClassFile($file)) {
			return array();
		}

		return array($file->getBaseName('.php'));
	}

	/**
	 * Determines whether a file contains a PHP class
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return boolean
	 */
	private function isPhpClassFile(SplFileInfo $file)
	{
		return '.php' === strtolower(substr($file->getPathname(), -4));
	}
}

?>