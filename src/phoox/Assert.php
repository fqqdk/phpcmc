<?php
/**
 * Holds the Assert class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * The Assert class is a Facade for creating assertions.
 */
class Assert
{
	/**
	 * @var PHPUnit_Framework_Assert
	 */
	private $assert;

	public function __construct(PHPUnit_Framework_Assert $assert)
	{
		$this->assert = $assert;
	}

	public function that($value, $constraint, $message='')
	{
		if ($constraint instanceof PHPUnit_Framework_Constraint) {
			$this->assert->assertThat($value, $constraint, $message);
		}

		if (is_callable($constraint)) {
			$this->assert->assertThat($value, $this->callback($construct), $message);
		}

		$this->assert->assertThat($value, $this->assert->equalTo($value), $message);
	}

	private function callback($callback)
	{
		return new CallbackConstraint($callback);
	}

	public function isEmpty($value, $message='')
	{
		$constraint = new CallbackConstraint($this->wrap('empty'), 'empty');
		$this->assert->assertThat($value, $constraint, $message);
	}

	private function wrap($construct)
	{
		return create_function('$value', 'return '.$construct.'($value);');
	}
}

?>