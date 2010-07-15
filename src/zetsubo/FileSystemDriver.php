<?php
/**
 * Holds the FileSystemDriver class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * FileSystemDriver is a facade enabling tests to easily create filesystem based
 * fixtures and assert upon them
 */
class FileSystemDriver
{
	/**
	 * @var string the common base directory of the fixture files
	 */
	private $baseDir;

	/**
	 * Constructor
	 *
	 * @param string $baseDir the common base directory of the fixture files
	 *
	 * @return FileSystemDriver
	 */
	public function __construct($baseDir)
	{
		$this->baseDir = $baseDir;
	}

	/**
	 * Absolutizes and formats a relative path
	 *
	 * @param string $path the relative path
	 *
	 * @return string
	 */
	public function absolute($path)
	{
		return $this->path($this->baseDir . $path);
	}

	/**
	 * The common base directory of the fixture files
	 *
	 * @return string
	 */
	public function baseDir()
	{
		return $this->baseDir;
	}

	/**
	 * Deletes a fixture directory
	 *
	 * @param string $dir the directory to delete
	 *
	 * @return void
	 */
	public function rmdir($dir)
	{
		try {
			$this->delTree($this->path($this->baseDir . $dir));
		} catch (UnexpectedValueException $ex) {
			// rmdir is quiet
		}
	}

	/**
	 * Creates a directory
	 *
	 * @param string $dir the directory to create under the base directory
	 *
	 * @return void
	 */
	public function mkdir($dir)
	{
		$path = $this->path($this->baseDir . $dir);
		if (file_exists($path) && is_dir($path)) {
			return;
		}
		mkdir($path, 0777, true);
	}

	/**
	 * Touches a file and optionally changes its contents
	 *
	 * @param string $file     the file to touch
	 * @param string $contents optional new contents for the file
	 *
	 * @return void
	 */
	public function touch($file, $contents='')
	{
		$dir = dirname($file);
		$this->mkdir($dir);
		$fileName = $this->path($this->baseDir . $file);
		touch($fileName);
		if (!empty($contents)) {
			file_put_contents($fileName, $contents);
		}
	}

	/**
	 * Checks whether a file is the special . or .. directory
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return boolean
	 */
	private function isDot(SplFileInfo $file)
	{
		return '/.'   == substr($file->getPathname(), -2) ||
			'/..' == substr($file->getPathname(), -3);
	}

	/**
	 * Deletes a directory recursively
	 *
	 * @param string $absDir the absolute path of the directory to delete
	 *
	 * @return void
	 */
	public function delTree($absDir)
	{
		$flags = RecursiveIteratorIterator::CHILD_FIRST;
		$rec   = new RecursiveDirectoryIterator($absDir);
		$iter  = new RecursiveIteratorIterator($rec, $flags);

		foreach ($iter as $file) {
			// Lunix lists dots (. and ..) as childs. Mac and win dont.
			// Also, SKIP_DOTS flag skips all "hidden" directories.
			if ($this->isDot($file)) {
				continue;
			}

			if ($file->isDir()) {
				rmdir($file->getPathname());
				continue;
			}

			if ($file->isFile()) {
				unlink($file->getPathname());
				continue;
			}

			trigger_error('Not a dir or a file');
		}

		//or else we run into locking issues on windows
		unset($iter, $rec);

		rmdir($absDir);
	}

	/**
	 * Checks whether a file is readable
	 *
	 * @param string $file the file
	 *
	 * @return boolean
	 */
	public function isReadable($file)
	{
		return is_readable($this->absolute($file));
	}

	/**
	 * Deletes a file
	 *
	 * @param string $file the file
	 *
	 * @return void
	 */
	public function unlink($file)
	{
		unlink($this->absolute($file));
	}

	/**
	 * Converts a file path that contains '/' characters as directory separators
	 * to a path that is more friendly with the underlying OS
	 *
	 * @param string $path the path to convert
	 *
	 * @return string
	 */
	public static function path($path)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}
}

?>