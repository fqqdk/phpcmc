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

	public function touch($file)
	{
		touch($this->baseDir . $file);
	}

	private function delTree($absDir)
	{
		foreach(glob($absDir . '*', GLOB_MARK ) as $absFile) {
			if(substr($absFile, -1) == '/' ) {
				$this->delTree($absFile);
			} else {
				unlink($absFile);
			}
		}

		if (is_dir($absDir)) {
			rmdir($absDir);
		}
	}
}

?>