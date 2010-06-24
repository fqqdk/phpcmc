<?php
/**
 * Holds the DirLoader class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * ClassLoader bound to a directory
 */
class DirLoader implements ClassLoader
{
	/**
	 * @var LoaderSession the classloader session
	 */
	private $session;

	/**
	 *
	 * @var string the directory the classloader is bound to
	 */
	private $dir;

	/**
	 * @var string debugging information: where the loader has been created
	 */
	private $created;

	/**
	 * Constructor
	 *
	 * @param LoaderSession $session the classloader session
	 * @param string        $dir     the directory the loader is bound to
	 * @param string        $created the location where the loader was created
	 *
	 * @return DirLoader
	 */
	public function __construct(LoaderSession $session, $dir, $created)
	{
		$this->session = $session;
		$this->dir     = $dir;
		$this->created = $created;
	}

	/**
	 * Loads a class from a file in the loader's directory
	 *
	 * @param string $className the name of the class to load
	 *
	 * @return boolean
	 */
	public function load($className)
	{
		$this->session->includeClassFile($this->path($this->dir . $className . '.php'));

		$success = $this->session->classExists($className);
		if ($success) {
			$this->session->success();
		}
		return $success;
	}

	/**
	 * Converts a path to OS-specific form
	 *
	 * @param string $path the path
	 *
	 * @return string
	 */
	private function path($path)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * The directory this loader is bound to
	 *
	 * @return string
	 */
	public function getDir()
	{
		return $this->dir;
	}

	/**
	 * The location the loader has been created at
	 *
	 * @return string
	 */
	public function getCreated()
	{
		return $this->created;
	}
}

?>