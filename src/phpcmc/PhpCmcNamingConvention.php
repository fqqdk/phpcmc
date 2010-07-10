<?php
/**
 * Holds the PhpCmcNamingConvention class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcNamingConvention
 */
interface PhpCmcNamingConvention
{
	/**
	 * Collects the PHP classes from a file
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return array
	 */
	public function collectPhpClassesFrom(SplFileInfo $file);
}

?>