<?php
/**
 * Holds the PearXmlBuilderTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PearXmlBuilderTest
 */
class PearXmlBuilderTest extends PhpCmcEndToEndTest implements FileWalker
{
	/**
	 * @var array dataset to build a directory tree for the test
	 */
	private $files = array(
		'/dir'              => true,
		'/dir/one.php'      => false,
		'/dir/two.php'      => false,
		'/otherdir'         => true,
		'/otherdir/bar'     => true,
		'/otherdir/bar/baz' => false,
		'/otherdir/foo'     => false,
		'/root.php'         => false,
		'/yetanother'       => true,
	);

	/**
	 * Nomen est omen
	 * 
	 * @test
	 * 
	 * @return void
	 */
	public function buildsValidXml()
	{
		$this->initFileSystem();

		foreach ($this->files as $file => $isDir) {
			if (!$isDir) {
				$this->fsDriver->touch($this->workDir . $file);
			}
		}

		$xml = new XMLWriter();
		$xml->openMemory();

		$builder = new PearXmlBuilder($xml);

		$builder->process($this);

		$output = Jabbar::prettifyXmlString($xml->flush());

		$expectedXml = '
			<dir name="dir">
				<file name="one.php" role="php" />
				<file name="two.php" role="php" />
			</dir>
			<dir name="otherdir">
				<dir name="bar">
					<file name="baz" role="php" />
				</dir>
				<file name="foo" role="php" />
			</dir>
			<file name="root.php"   role="php" />
			<file name="yetanother" role="php" />
		';

		$this->assert->that($output, $this->logicalNot($this->isEmpty()));
		$this->assertXmlStringEqualsXmlString(
			'<root>'.$expectedXml.'</root>',
			'<root>'.$output.'</root>'
		);
		$this->cleanupOnSuccess();
	}

	/**
	 * Mock method
	 *
	 * @param FileWalkListener $listener listener
	 *
	 * @return void
	 */
	public function walk(FileWalkListener $listener)
	{
		foreach ($this->files as $fileName => $isDir) {
			$path = $this->fsDriver->absolute($this->workDir.$fileName);
			$listener->foundFile(new SplFileInfo($path));
		}
	}
}

?>