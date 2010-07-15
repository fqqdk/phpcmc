<?php
/**
 * Holds the JabbarTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of JabbarTest
 */
class JabbarTest extends PhpCmcEndToEndTest
{
	/**
	 * @var PhpScriptRunner
	 */
	private $runner;

	/**
	 * Sets up the fixtures
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		$this->runner = new PhpScriptRunner();
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function generatesXmlContent()
	{
		$this->initFileSystem();
		$files = array(
			'/root.php',
			'/dir/one.php',
			'/dir/two.php',
			'/otherdir/foo',
			'/otherdir/bar/baz',
			'/yetanother',
		);
		foreach ($files as $file) {
			$this->fsDriver->touch($this->workDir . $file);
		}

		$output = $this->runner->runPhpScriptWithPrepend(
			BASE_DIR.'src/jabbar.php', array(
				$this->fsDriver->absolute($this->workDir),
			), array(), SRC_DIR, BOOTSTRAP_FILE
		);

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
}

?>