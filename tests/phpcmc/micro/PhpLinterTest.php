<?php
/**
 * Holds the PhpLinterTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpLinterTest
 */
class PhpLinterTest extends PhpCmcEndToEndTest
{
	private $mem;

	protected function setUp()
	{
		parent::setUp();
		$this->mem = fopen('php://memory', 'w');
	}

	protected function tearDown()
	{
		parent::tearDown();
		fclose($this->mem);
	}
	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function checksTheSyntaxOfAFile()
	{
		$file   = new SplFileInfo(__file__);
		$linter = new PhpLinter(new OutputStream($this->mem));

		$this->assert->that($linter->checkSyntax($file), $this->isTrue());
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function returnsFalseWhenFileContainsParseErrors()
	{
		$file = 'syntaxerror.php';
		$this->fsDriver->touch($file, '<?php this is invalid ?'.'>');
		$linter = new PhpLinter(new OutputStream($this->mem));

		$result = $linter->checkSyntax(new SplFileInfo($this->fsDriver->absolute($file)));

		$this->assert->that($result, $this->isFalse());
	}

}

?>