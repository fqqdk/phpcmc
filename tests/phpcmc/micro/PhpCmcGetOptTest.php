<?php
/**
 * Holds the PhpCmcGetOptTest class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PhpCmcGetOptTest
 *
 * @group micro
 */
class PhpCmcGetOptTest extends ZetsuboTestCase
{
	/**
	 * @var PhpCmcOptsParser the CUT
	 */
	private $opts;

	/**
	 * Sets up the fixtures
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->opts = new PhpCmcOptsParser();
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 * @expectedException PhpCmcException 
	 *
	 * @return void
	 */
	public function directoryArgumentIsMandatory()
	{
		$argv = array();
		$this->opts->parse($argv);
	}

	/**
	 * Nomen est omen
	 *
	 * @test
	 *
	 * @return void
	 */
	public function defaultOptionsAreCalculated()
	{
		$argv = array('dummy_the_script', 'dummy_dir');
		$opts = $this->opts->parse($argv);

		$this->assert->that($opts, $this->assoc(array(
			'format' => 'summary',
			'naming' => 'filebasename',
		)));
	}
}

?>