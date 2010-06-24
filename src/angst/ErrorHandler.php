<?php
/**
 * Holds the ErrorHandler class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ErrorHandler
 */
interface ErrorHandler
{
	/**
	 * Handles a PHP error
	 *
	 * @param int    $code    the error code
	 * @param string $message the error message
	 *
	 * @return boolean
	 */
	public function handle($code, $message);
}

?>