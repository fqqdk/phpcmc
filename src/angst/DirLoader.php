<?php
/**
 * Holds the DirLoader class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of DirLoader
 */
class DirLoader implements ClassLoader
{
	private $session;
	private $dir;
	private $created;

	public function __construct(LoaderSession $session, $dir, $created) {
		$this->session = $session;
		$this->dir     = $dir;
		$this->created = $created;
	}

	public function load($className) {
		$this->session->includeClassFile($this->path($this->dir . $className . '.php'));

		$success = $this->session->classExists($className);
		if ($success) {
			$this->session->success();
		}
		return $success;
	}

	private function path($path)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function getCreated()
	{
		return $this->created;
	}
}

?>