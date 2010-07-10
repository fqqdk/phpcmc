<?php
/**
 * Holds the FileSystemDriverIntegrationTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Holds test cases for the FileSystemDriver
 */
class FileSystemDriverIntegrationTest extends ZetsuboTestCase
{
	/**
	 * Absolutizes and converts a path using the underlying OS's
	 * directory separator
	 *
	 * @param string $path the path
	 *
	 * @return string
	 */
	private function path($path)
	{
		return FileSystemDriver::path($path);
	}

	/**
	 * Tests that rmdir() can delete a directory
	 *
	 * @test
	 *
	 * @return void
	 */
	public function rmdirDeletesADirectory()
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
	 * Tests that delTree() deletes directories and files recursively
	 *
	 * @test
	 *
	 * @return void
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
	 * Tests that delTree() deletes recursive files
	 *
	 * @test
	 *
	 * @return void
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