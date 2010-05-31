<?php

class FileSystemDriverIntegrationTest extends PhooxTestCase
{
	/**
	 * @test
	 */
	public function rmdirShouldWork()
	{
		if (file_exists(WORK_DIR.'dirToDelete/foo.txt')) {
			unlink(WORK_DIR.'dirToDelete/foo.txt');
		}
		if (is_dir(WORK_DIR.'dirToDelete')) {
			rmdir(WORK_DIR.'dirToDelete');
		}
		mkdir(WORK_DIR.'dirToDelete');
		file_put_contents(WORK_DIR.'dirToDelete/foo.txt','lorem ipsum');
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->rmdir('dirToDelete');
	}

	/**
	 * @test
	 */
	public function delTreeDeletesRecursively()
	{
		$dir = WORK_DIR.'dirTreeToDelete';
		$file = $dir.'/foo.txt';
		if (file_exists($file)) {
			unlink($file);
		}
		if (is_dir($dir)) {
			rmdir($dir);
		}
		mkdir($dir);
		file_put_contents($file,'lorem ipsum');
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->delTree($dir);
		$this->assertFalse(is_dir($dir));
	}

	/**
	 * @test
	 */
	public function delTreeDeletesHiddenFiles()
	{
		$dir              = WORK_DIR . 'dirTreeWithHiddenFiles/';
		$hiddenSubDir     = $dir . '.hiddendir/';

		$hiddenFile       = $dir . '.hidden';
		$hiddenSubDirFile = $hiddenSubDir . 'file';

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
	}
	
}

?>
