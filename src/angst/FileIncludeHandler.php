<?php
/**
 * Holds the FileIncludeHandler class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of FileIncludeHandler
 */
class FileIncludeHandler implements ErrorHandler {
	private $files = array();

	private function fileName() {
		return $this->files[count($this->files) - 1];
	}

	public function startIncluding($fileName) {
		$thisHandler = array($this, 'handle');

		$oldHandler  = set_error_handler($thisHandler);
		if ($oldHandler === $thisHandler) {
			restore_error_handler();
		}
		array_push($this->files, $fileName);
	}

	/**
	 *
	 * @param <type> $fileName
	 *
	 * @todo what if an included file tampers with the errorhandling?
	 *
	 * @return void
	 */
	public function stopIncluding($fileName) {
		array_pop($this->files);
		restore_error_handler();
	}

	public function restore() {
		$this->files      = array();
	}

	private function shouldSwallow($errorCode) {
		return (bool) ($errorCode & error_reporting());
	}

	private function isOwnFileIncludeWarning($errorMessage) {
		if (empty($this->files)) {
			return false;
		}

		return (bool) preg_match('#'.preg_quote($this->fileName(), '#').'#', $errorMessage);
	}

	/**
	 *
	 * @param <type> $errorCode
	 * @param <type> $errorMessage
	 *
	 * @todo check shouldswallow method
	 *
	 * @return <type>
	 */
	public function handle($errorCode, $errorMessage) {
		if (false === $this->shouldSwallow($errorCode)) {
			return false;
		}
		return $this->isOwnFileIncludeWarning($errorMessage);
	}
}

?>