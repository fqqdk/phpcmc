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
		if (file_exists(WORK_DIR.'dirTreeToDelete/foo.txt')) {
			unlink(WORK_DIR.'dirTreeToDelete/foo.txt');
		}
		if (is_dir(WORK_DIR.'dirTreeToDelete')) {
			rmdir(WORK_DIR.'dirTreeToDelete');
		}
		mkdir(WORK_DIR.'dirTreeToDelete');
		file_put_contents(WORK_DIR.'dirTreeToDelete/foo.txt','lorem ipsum');
		$fsDriver = new FileSystemDriver(WORK_DIR);
		$fsDriver->delTree(WORK_DIR.'dirTreeToDelete');
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
