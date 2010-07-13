<?php
/**
 * Holds the PhpLinterTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpLinterTest
 *
 * @group micro
 */
class PhpLinterTest extends PhpCmcEndToEndTest
{
	/**
	 * @var resource handle to the memory stream that is used instead of stderr
	 */
	private $listener;

	/**
	 * Sets up the fixtures
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->listener = $this->mock('PhpCmcListener', get_class_methods('PhpCmcListener'));
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
		$linter = new PhpLinter($this->listener);

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
		$linter = new PhpLinter($this->listener);

		$result = $linter->checkSyntax(new SplFileInfo($this->fsDriver->absolute($file)));

		$this->assert->that($result, $this->isFalse());
	}

}

?>