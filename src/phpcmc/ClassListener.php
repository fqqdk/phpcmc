<?php
/**
 * Holds the ClassListener class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassListener
 */
interface ClassListener
{
	/**
	 * This event is fired when the collector finds a class
	 *
	 * @param string $className the name of the found class
	 * @param string $file      the path of the file in which the class has been found
	 *
	 * @return void
	 */
	public function classFound($className, $file);

	/**
	 * This event is fired when a duplicate class is found
	 *
	 * @param string $className    the duplicate class
	 * @param string $file         the file in which the duplicate class has been found
	 * @param string $originalFile the file in which the class has been found the first time
	 *
	 * @return void
	 */
	public function duplicate($className, $file, $originalFile);
}

?>