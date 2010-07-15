<?php
/**
 * Holds the ZetsuboTestCase class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Common base class for test cases that want to utilize zetsubo functionality
 */
abstract class ZetsuboTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Assert the assertion builder
	 */
	protected $assert;

	/**
	 * Sets the fixtures up
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->assert   = new Assert($this);
	}

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
	public function assoc(array $constraintMap)
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
		$valueConstraint = $this->wrap($valueConstraint);

		return new ArrayHasKeyWithValue($arrayKey, $valueConstraint);
	}

	/**
	 * Creates a constraint that will be fullfilled if the tested value is empty()
	 *
	 * @return PHPUnit_Framework_Constraint
	 */
	public function isEmpty()
	{
		$callback = create_function('$arg', 'return empty($arg);');
		return new CallbackConstraint($callback, 'empty');
	}

	/**
	 * Autowraps a value in an EqualTo constraint
	 *
	 * @param mixed $constraint the value
	 * 
	 * @return PHPUnit_Framework_Constraint
	 */
	public function wrap($constraint)
	{
		if ($constraint instanceof PHPUnit_Framework_Constraint) {
			return $constraint;
		}

		return $this->equalTo($constraint);
	}

	/**
	 * Skips the test if an ini setting doesn't fulfill a constraint
	 *
	 * @param string $iniKey          the ini setting's key
	 * @param mixed  $valueConstraint the constraint on the value for the ini setting
	 *
	 * @return void
	 */
	public function requireIniSetting($iniKey, $valueConstraint)
	{
		$valueConstraint = $this->wrap($valueConstraint);

		$iniValue = ini_get($iniKey);
		if ($valueConstraint->evaluate($iniValue)) {
			return;
		};

		$message = 'this test needs ini setting "%s" set to a value that %s, but %s found';

		$this->markTestSkipped(sprintf(
			$message, $iniKey, $valueConstraint->toString(), 
			PHPUnit_Util_Type::shortenedExport($iniValue)
		));
	}

	/**
	 * Skips the test if a "boolean type" ini switch isn't turned on
	 *
	 * @param string $iniKey the ini setting's key
	 *
	 * @return void
	 */
	public function requireIniSwitch($iniKey)
	{
		if (ini_get($iniKey)) {
			return;
		}

		$message = 'this test needs ini setting "%s" turned on';

		$this->markTestSkipped(sprintf($message, $iniKey));
	}

	/**
	 * Mocks a class the way classes should be mocked by default:
	 * - no parent constructor or clone calls
	 * - autoloader is called if the class is not loaded yet
	 *
	 * @param string $className the class to mock
	 * @param array  $methods   the methods to mock
	 *
	 * @return object the mock object
	 */
	public function mock($className, $methods=array())
	{
		if (is_string($methods)) {
			$methods = array($methods);
		}

		return $this->getMock($className, $methods, array(), '', false, false, true);
	}

	public function name($name)
	{
		return new NameConstraint($name);
	}
}

?>