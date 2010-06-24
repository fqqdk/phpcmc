<?php
/**
 * Holds the FileIncludeHandler class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Special ErrorHandler that is used only when a DirLoader attempts to load a
 * class from a directory to suppress warnings that are expected to occur when
 * the DirLoader attempts to include a file that does not exist.
 */
class FileIncludeHandler implements ErrorHandler
{
	/**
	 * @var array the file stack
	 */
	private $files = array();

	/**
	 * The name of the file being included
	 *
	 * @return string
	 */
	private function fileName()
	{
		return $this->files[count($this->files) - 1];
	}

	/**
	 * Tells the handler that the inclusion of a file has been started
	 *
	 * @param string $fileName the file name
	 *
	 * @return void
	 */
	public function startIncluding($fileName)
	{
		$thisHandler = array($this, 'handle');

		$oldHandler  = set_error_handler($thisHandler);
		if ($oldHandler === $thisHandler) {
			restore_error_handler();
		}
		array_push($this->files, $fileName);
	}

	/**
	 * Tells the handler that there will be no more attempts to include the file
	 *
	 * @param string $fileName the file name
	 *
	 * @todo what if an included file tampers with the errorhandling?
	 *
	 * @return void
	 */
	public function stopIncluding($fileName)
	{
		array_pop($this->files);
		restore_error_handler();
	}

	/**
	 * Starts a new file stack
	 *
	 * @return void
	 */
	public function restore()
	{
		$this->files      = array();
	}

	/**
	 * If the handler should swallow an error due to error_reporting settings or
	 * usage of the @ operator
	 *
	 * @param int $errorCode the type of the error
	 *
	 * @return boolean
	 */
	private function shouldSwallow($errorCode)
	{
		return (bool) ($errorCode & error_reporting());
	}

	/**
	 * Determines whether the error message is a warning caused by a failed
	 * attempt to include the current file on the file stack
	 *
	 * @param string $errorMessage the error message
	 * 
	 * @return boolean
	 */
	private function isOwnFileIncludeWarning($errorMessage)
	{
		if (empty($this->files)) {
			return false;
		}

		return (bool) preg_match('#'.preg_quote($this->fileName(), '#').'#', $errorMessage);
	}

	/**
	 * Handles a PHP error
	 *
	 * @param int    $code    the error code
	 * @param string $message the error message
	 *
	 * @todo check shouldswallow method
	 *
	 * @return boolean
	 */
	public function handle($code, $message)
	{
		if (false === $this->shouldSwallow($code)) {
			return false;
		}
		return $this->isOwnFileIncludeWarning($message);
	}
}

?>