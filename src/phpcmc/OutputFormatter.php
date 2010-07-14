<?php
/**
 * Holds the OutputFormatter class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of OutputFormatter
 */
interface OutputFormatter
{
	/**
	 * The header of the output
	 *
	 * @return string
	 */
	public function header();

	/**
	 * One formatted class entry
	 *
	 * @param string $className the class
	 * @param string $file      the file
	 *
	 * @return string
	 */
	public function classEntry($className, $file);

	/**
	 * The footer of the output
	 *
	 * @return string
	 */
	public function footer();
}

?>