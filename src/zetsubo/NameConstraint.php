<?php
/**
 * Holds the NameConstraint class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of NameConstraint
 */
class NameConstraint extends PHPUnit_Framework_Constraint
{
	private $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function evaluate($other)
	{
		return is_string($other) && $other == $this->name;
	}

	public function toString()
	{
		return $this->name;
	}
}

?>