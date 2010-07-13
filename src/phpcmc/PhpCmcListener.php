<?php
/**
 * Holds the PhpCmcListener class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of PhpCmcListener
 */
interface PhpCmcListener
{
	public function error($error);
	public function classFound($className, $file);
	public function duplicate($className, $file, $originalFile);
	public function searchStarted();
	public function searchCompleted();
}

?>