<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PearXmlBuilderTest
 *
 * @author fqqdk
 */
class PearXmlBuilderTest extends PhpCmcEndToEndTest implements FileWalker
{
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

		$builder = new PearXmlBuilder($xml);

		$builder->start();
		$builder->process($this);
		$builder->finish();

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

	public function walk(FileWalkListener $listener)
	{
		foreach ($this->files as $fileName => $isDir) {
			$path = $this->fsDriver->absolute($this->workDir.$fileName);
			$listener->foundFile(new SplFileInfo($path));
		}
	}
}

?>