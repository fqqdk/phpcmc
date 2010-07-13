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
	public function header();
	public function classEntry($file, $className);
	public function footer();
}

?>