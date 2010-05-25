<?php
/**
 * Holds the Constraint class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of Constraint
 */
class CallbackConstraint extends PHPUnit_Framework_Constraint
{
	private $callback;
	private $name;

	public function __construct($callback, $name=null)
	{
		$this->callback = $callback;
		$this->name     = $name;
	}

	public function evaluate($value)
	{
		return (bool)call_user_func($this->callback, $value);
	}
	
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