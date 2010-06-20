<?php
/**
 * Holds the FileSystemDriver class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of FileSystemDriver
 */
class FileSystemDriver
{
	private $baseDir;

	public function  __construct($baseDir)
	{
		$this->baseDir = $baseDir;
	}

	public function absolute($path)
	{
		return $this->path($this->baseDir . $path);
	}

	public function baseDir()
	{
		return $this->baseDir;
	}

	public function rmdir($dir)
	{
		try {
			$this->delTree($this->path($this->baseDir . $dir));
		} catch (UnexpectedValueException $ex) {
			// rmdir is quiet
		}
	}

	public function mkdir($dir)
	{
		mkdir($this->path($this->baseDir . $dir), 0777, true);
	}

	public function touch($file, $contents='')
	{
		$fileName = $this->path($this->baseDir . $file);
		touch($fileName);
		file_put_contents($fileName, $contents);
	}

	private function isDot(SplFileInfo $file)
	{
		return '/.'   == substr($file->getPathname(), -2) ||
			'/..' == substr($file->getPathname(), -3);
	}

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

	public function isReadable($file)
	{
		return is_readable($this->absolute($file));
	}

	public function unlink($file)
	{
		unlink($this->absolute($file));
	}

	public static function path($path)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}
}

?>