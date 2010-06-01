<?php

class FileSystemDriverIntegrationTest extends PhooxTestCase
{
	private function path($path)
	{
		return FileSystemDriver::path($path);
	}
	/**
	 * @test
	 */
	public function rmdirShouldWork()
	{
	    $dir  = WORK_DIR . 'dirToDelete';
	    $file = WORK_DIR . 'dirToDelete/foo.txt';

		if (file_exists($file)) {
			unlink($file);
		}
		if (is_dir($dir)) {
			rmdir($dir);
		}
		mkdir($dir);
		file_put_contents($file, 'lorem ipsum');
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->rmdir('dirToDelete');
		
		$this->assertFalse(is_file($file), 'The file should have been deleted.');
		$this->assertFalse(is_dir($dir), 'The dir should have been deleted');
	}

	/**
	 * @test
	 */
	public function delTreeDeletesRecursively()
	{
		$dir  = WORK_DIR . 'dirTreeToDelete';
		$file = $dir . '/foo.txt';

		if (file_exists($file)) {
			unlink($file);
		}
		if (is_dir($dir)) {
			rmdir($dir);
		}
		mkdir($dir);
		file_put_contents($file, 'lorem ipsum');
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->delTree($dir);
		$this->assertFalse(is_file($file));
		$this->assertFalse(is_dir($dir));
	}

	/**
	 * @test
	 */
	public function delTreeDeletesHiddenFiles()
	{
		$dir              = $this->path(WORK_DIR . 'dirTreeWithHiddenFiles');
		$hiddenSubDir     = $this->path($dir . '/.hiddendir');

		$hiddenFile       = $this->path($dir . '/.hidden');
		$hiddenSubDirFile = $this->path($hiddenSubDir . '/file');

		if (file_exists($hiddenFile)) {
			unlink($hiddenFile);
		}

		if (file_exists($hiddenSubDirFile)) {
			unlink($hiddenSubDirFile);
		}

		if (is_dir($hiddenSubDir)) {
			rmdir($hiddenSubDir);
		}

		if (is_dir($dir)) {
			rmdir($dir);
		}

		mkdir($dir);
		mkdir($hiddenSubDir);

		file_put_contents($hiddenFile,       'hidden');
		file_put_contents($hiddenSubDirFile, 'hidden');

		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->delTree($dir);
		
		$this->assertFalse(is_file($hiddenFile));
		$this->assertFalse(is_file($hiddenSubDirFile));
		
		$this->assertFalse(is_dir($hiddenSubDir));
		$this->assertFalse(is_dir($dir));
	}
	
}

?>
