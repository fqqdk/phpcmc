<?php
/**
 * Holds the Assert class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * The Assert class is a Facade for creating constraints and checking them.
 */
class Assert
{
	/**
	 * @var PHPUnit_Framework_Assert the assertion builder
	 */
	private $assert;

	/**
	 * Constructor
	 *
	 * @param PHPUnit_Framework_Assert $assert the assertion builder
	 *
	 * @return Assert
	 */
	public function __construct(PHPUnit_Framework_Assert $assert)
	{
		$this->assert = $assert;
	}

	/**
	 * Generic assert method.
	 *
	 * Evaluates the passed constraint
	 *
	 * @param mixed                        $variable   the variable to check
	 * @param PHPUnit_Framework_Constraint $constraint the constraint to enforce
	 * @param string                       $message    optional failure message
	 *
	 * @return void
	 */
	public function that($variable, $constraint, $message='')
	{
		if ($constraint instanceof PHPUnit_Framework_Constraint) {
			$this->assert->assertThat($variable, $constraint, $message);
		}

		if (is_callable($constraint)) {
			$this->assert->assertThat($variable, $this->callback($construct), $message);
		}

		$this->assert->assertThat($variable, $this->assert->equalTo($variable), $message);
	}

	public function logicalNot($constraint)
	{
		return $this->assert->logicalNot($constraint);
	}

	/**
	 * Creates a callback constraint using the given callback
	 *
	 * @param callable $callback the callback on which the constraint is based
	 *
	 * @return CallbackConstraint
	 */
	private function callback($callback)
	{
		return new CallbackConstraint($callback);
	}

	/**
	 * Asserts that a value is empty
	 *
	 * @param mixed  $variable the value to check
	 * @param string $message  additional failure description
	 *
	 * @return void
	 */
	public function isEmpty($variable, $message='')
	{
		$constraint = new CallbackConstraint($this->wrap('empty'), 'empty');
		$this->assert->assertThat($variable, $constraint, $message);
	}

	/**
	 * Wraps a function-like php construct in an anonymous callback function
	 *
	 * @param string $construct the php construct ('empty', 'isset', etc)
	 *
	 * @return callable
	 */
	private function wrap($construct)
	{
		return create_function('$value', 'return '.$construct.'($value);');
	}
}

?>