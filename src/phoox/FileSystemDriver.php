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
		return $this->baseDir . $path;
	}

	public function baseDir()
	{
		return $this->baseDir;
	}

	public function rmdir($dir)
	{
		$this->delTree($this->baseDir . $dir);
	}

	public function mkdir($dir)
	{
		mkdir($this->baseDir . $dir, 0777, true);
	}

	public function touch($file, $contents='')
	{
		$fileName = $this->baseDir . $file;
		touch($fileName);
		file_put_contents($fileName, $contents);
	}

	public function delTree($absDir)
	{
		try {
			$rec  = new RecursiveDirectoryIterator($absDir);
		} catch(UnexpectedValueException $ex) {
			return;
		}

		$iter = new RecursiveIteratorIterator(
			$rec, RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iter as $file) {
			if ($file->isDir()) {
				rmdir($file->getPathname());
				continue;
			}
			unlink($file->getPathname());
		}
	}

	public function isReadable($file)
	{
		return is_readable($this->absolute($file));
	}
	
	public function unlink($file)
	{
		unlink($this->absolute($file));
	}
}

?>