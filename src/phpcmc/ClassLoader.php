<?php
/**
 * Holds the ClassLoader class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

if (interface_exists('ClassLoader')) {
	return false;
}

/**
 * Description of ClassLoader
 */
interface ClassLoader
{
	/**
	 * Loads a class into memory
	 *
	 * Returns true on success, false if the class couldn't be loaded
	 *
	 * @param string $className the name of the class
	 *
	 * @return boolean
	 */
	public function load($className);
}

?>