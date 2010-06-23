<?php
/**
 * Holds the Constraint class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * CallbackConstraint is a generic constraint that can be used to assert
 * that various boolean returning functions return true for a value
 */
class CallbackConstraint extends PHPUnit_Framework_Constraint
{
	/**
	 * @var callable the wrapped callback
	 */
	private $callback;

	/**
	 * @var string human-readable name for the object.
	 */
	private $name;

	/**
	 * Constructor
	 *
	 * @param callable $callback the callback to wrap
	 * @param string   $name     human-readable name of the callback
	 */
	public function __construct($callback, $name=null)
	{
		$this->callback = $callback;
		$this->name     = $name;
	}

	/**
	 * Evaluates the constraint
	 *
	 * @param mixed $variable the value to check
	 *
	 * @return boolean
	 */
	public function evaluate($variable)
	{
		return (bool)call_user_func($this->callback, $variable);
	}

	/**
	 * Throws a specific exception to report failure
	 *
	 * @param mixed   $other   the value that failed to evaluate
	 * @param string  $message optional failure message
	 * @param boolean $not     whether the constraint is negated
	 *
	 * @return void
	 * @throws PHPUnit_Framework_ExpectationFailedException
	 */
	public function fail($other, $message, $not=false)
	{
		throw new PHPUnit_Framework_ExpectationFailedException(
			sprintf('Failed asserting that %s', $this->failureDescription(
				$other, $message, $not
			))
		);
	}

	protected function failureDescription($other, $message, $not)
	{
		if ($message) {
			return $message . PHP_EOL . '(in other words: ' . $this->failureDescription($other, null, $not) . ')';
		}

		return sprintf(
			'%s(%s)',
			$this->describeCallback($this->callback),
			$this->describe($other)
		);
	}

	private function describe($value)
	{
		return PHPUnit_Util_Type::shortenedString($value);
	}

	private function describeCallback($callback)
	{
		if (null !== $this->name) {
			return $this->name;
		}
		if (is_array($callback)) {
			if (is_string($callback[0])) {
				return $callback[0] . '::' . $callback[1]; 
			} 
			return $this->shorten($callback[0]) . '->' . $callback[1];
		}
		return $callback;
	}

	public function toString()
	{
		return 'is ' . $this->describeCallback($this->callback);
	}
}

?>