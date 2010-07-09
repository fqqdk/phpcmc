<?php
/**
 * Holds the PhooxTest class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Common base class for test cases that want to utilize phoox functionality
 */
class PhooxTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * Gets the full path of the file that contains the class
	 *
	 * @param string $className the name of the class
	 *
	 * @return string
	 */
	protected function getFileOfClass($className)
	{
		$rc = new ReflectionClass($className);
		return $rc->getFileName();
	}

	/**
	 * Shortcut for creating a constraint against an associative array. An array
	 * will fulfill the created constraints if it has all the keys that the given
	 * constraintmap contains and the values corresponding to these keys fulfill
	 * the constraints that are passed as values in the constraintmap
	 *
	 * @param array $constraintMap map of constraints
	 *
	 * @return PHPUnit_Framework_Constraint
	 */
	protected function assoc(array $constraintMap)
	{
		$constraints = array();
		foreach ($constraintMap as $arrayKey => $constraint) {
			$constraints []= $this->arrayHasKeyWithValue($arrayKey, $constraint);
		}

		return call_user_func_array(array($this, 'logicalAnd'), $constraints);
	}

	/**
	 * Creates an ArrayHasKeyWithValue
	 *
	 * @param mixed                        $arrayKey        the key that should
	 *                                                      exist in the array
	 * @param PHPUnit_Framework_Constraint $valueConstraint the constraint for
	 *                                                      the value
	 *
	 * @return ArrayHasKeyWithValue
	 */
	protected function arrayHasKeyWithValue($arrayKey, $valueConstraint)
	{
		if (!$valueConstraint instanceof PHPUnit_Framework_Constraint) {
			$valueConstraint = $this->equalTo($valueConstraint);
		}

		return new ArrayHasKeyWithValue($arrayKey, $valueConstraint);
	}

	/**
	 * Creates a constraint that will be fullfilled if the tested value is empty()
	 *
	 * @return PHPUnit_Framework_Constraint
	 */
	protected function isEmpty()
	{
		$callback = create_function('$arg', 'return empty($arg);');
		return new CallbackConstraint($callback, 'empty');
	}
}

?>