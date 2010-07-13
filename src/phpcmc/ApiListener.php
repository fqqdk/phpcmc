<?php
/**
 * Holds the ApiListener class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ApiListener
 */
class ApiListener implements PhpCmcListener
{
	public function error($error) {}
	public function classFound($className, $file) {}
	public function duplicate($className, $file, $originalFile) {}
	public function searchStarted() {}
	public function searchCompleted() {}
}

?>