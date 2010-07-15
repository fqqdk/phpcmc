<?php
/**
 * Holds the ValueObjectConstraint class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ValueObjectConstraint
 */
class ValueObjectConstraint extends CallbackConstraint
{
	public function __construct($method, $constraint, $name='object')
	{
		parent::__construct(
			array($this, 'callback'),
			sprintf('%s->%s()', $name, $method)
		);

		$this->method     = $method;
		$this->constraint = $constraint;
	}

	public function callback($other) {
		return $this->constraint->evaluate($other->{$this->method}());
	}
}

?>