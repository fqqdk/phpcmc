<?php
/**
 * Holds the ClassLoader class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassLoader
 */
interface ClassLoader
{
	/**
	 * Loads a class into memory
	 *
	 * @param string $className the name of the class
	 *
	 * @return void
	 */
	public function load($className);
}

?>