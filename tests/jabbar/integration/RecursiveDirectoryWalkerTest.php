<?php
/**
 * Holds the RecursiveDirectoryWalkerTest class
 *
 * @author fqqdk
 */

/**
 * Description of RecursiveDirectoryWalkerTest
 */
class RecursiveDirectoryWalkerTest extends PhpCmcEndToEndTest implements FileWalkListener
{
	private $found = array();
	private $files = array(
		 'c', '2', 'foo', '1', 'a', 'b'
	);

	/**
	 * Nomen est omen
	 * 
	 * @test
	 * 
	 * @return void
	 */
	public function filesAreWalkedAlphabeticallyAndDotsAreSkipped()
	{
		$this->initFileSystem();

		$this->fsDriver->touch($this->workDir.'/a');
		$this->fsDriver->touch($this->workDir.'/b');
		$this->fsDriver->touch($this->workDir.'/c/1');
		$this->fsDriver->touch($this->workDir.'/c/2/foo');

		$walker = new RecursiveDirectoryWalker($this->fsDriver->absolute($this->workDir));

		$walker->walk($this);

		$this->assert->that(
			count($this->found), $this->equalTo(6),
			var_export($this->found, true)
		);

		$this->cleanupOnSuccess();
	}

	public function foundFile(SplFileInfo $file) {
		array_push($this->found, $file->getPathname());

		$this->assert->that($file->getFilename(), $this->notDot(), var_export($this->found, true));
		$this->assert->that($file->getFilename(), $this->equalTo(
				$this->files[count($this->found)-1]
		), var_export($this->found, true));
	}

	private function notDot()
	{
		return $this->logicalAnd(
			$this->logicalNot($this->equalTo('.')),
			$this->logicalNot($this->equalTo('..'))
		);
	}
}

?>