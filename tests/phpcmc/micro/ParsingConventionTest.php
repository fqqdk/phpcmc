<?php
/**
 * Holds the ParsingConventionTest
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ParsingConventionTest
 */
class ParsingConventionTest extends ZetsuboTestCase
{
	/**
	 * Nomen est omen
	 *
	 * @test
	 * 
	 * @return void
	 */
	public function findsClassesInTheFile()
	{
		$file   = new SplFileInfo(__file__);
		$linter = $this->mock('PhpLinter', array('checkSyntax'));
		$parser = new ParsingConvention($linter);

		$linter->expects($this->once())->method('checkSyntax')
			->with($file)->will($this->returnValue(true));

		$classes = $parser->collectPhpClassesFrom($file);

		$this->assert->that($classes, $this->contains(__class__));
		$this->assert->that($classes, $this->contains('ParsingConvention_DummyClass'));
	}
}

class ParsingConvention_DummyClass {}

?>