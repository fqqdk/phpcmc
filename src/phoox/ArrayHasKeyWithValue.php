<?php
/**
 * Holds the ArrayHasKeyWithValue class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Constraint that specifies that a key exists in an array and the value for the
 * key fulfils some other constraint
 */
class ArrayHasKeyWithValue extends PHPUnit_Framework_Constraint_ArrayHasKey
{
	/**
	 * @var PHPUnit_Framework_Constraint the constraint for the value
	 */
	private $valueConstraint;

	/**
	 * Constructor
	 *
	 * @param mixed                        $arrayKey        the key that should
	 *                                                      exist in the array
	 * @param PHPUnit_Framework_Constraint $valueConstraint the constraint for
	 *                                                      the value
	 *
	 * @return ArrayHasKeyWithValue
	 */
	public function __construct($arrayKey, $valueConstraint)
	{
		parent::__construct($arrayKey);
		$this->valueConstraint = $valueConstraint;
	}

	/**
	 * Evaluates the constraint for parameter $other. Returns TRUE if the
	 * constraint is met, FALSE otherwise.
	 *
	 * @param mixed $other Value or object to evaluate.
	 * @return bool
	 */
	public function evaluate($other)
	{
		return is_array($other)
			&& parent::evaluate($other)
			&& $this->valueConstraint->evaluate($other[$this->key]);
	}

	/**
	 * Returns a string representation of the constraint.
	 *
	 * @return string
	 */
    public function toString()
    {
		return parent::toString() . ' and the corresponding value '
			. $this->valueConstraint->toString();
    }

	/**
	 * @param mixed   $other
	 * @param string  $description
	 * @param boolean $not
	 */
	protected function customFailureDescription($other, $description, $not)
	{
		return sprintf(
			'Failed asserting that %s %s.',
			PHPUnit_Util_Type::shortenedExport($other),
			$this->toString()
		);
	}
}

?>