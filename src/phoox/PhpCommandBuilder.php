<?php
/**
 * Holds the PhpCommandBuilder class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of PhpCommandBuilder
 */
class PhpCommandBuilder extends ShellCommandBuilder
{
	/**
	 * @var array
	 */
	private $phpArgs = array();

	public function __construct($executable='php')
	{
		parent::__construct($executable, false);
	}

	public function addPhpArg($arg)
	{
		$this->phpArgs[] = escapeshellarg($arg);

		return $this;
	}

	public function addPhpArgs(array $args)
	{
		foreach ($args as $arg) {
			$this->addPhpArg($arg);
		}

		return $this;
	}

	public function addPhpProperty($propertyName, $propertyValue)
	{
		$this->phpArgs[] = $propertyName . ' ' . escapeshellarg($propertyValue);

		return $this;
	}

	public function addPhpProperties(array $properties)
	{
		foreach ($properties as $propertyName => $propertyValue) {
			$this->addPhpProperty($propertyName, $propertyValue);
		}

		return $this;
	}

	public function addIniVar($iniVarName, $iniVarValue)
	{
		$this->phpArgs[] = '-d '.$iniVarName.'="'.escapeshellarg($iniVarValue).'"';

		return $this;
	}

	public function addIniVars(array $iniVars) {
		foreach ($iniVars as $iniVarName => $iniVarValue) {
			$this->addIniVar($iniVarName, $iniVarValue);
		}

		return $this;
	}

	public function runWith(ShellCommandRunner $runner, $stdin='', $env=array())
	{
		$this->args = array_merge($this->phpArgs, array('--'), $this->args);

		return parent::runWith($runner, $stdin, $env);
	}
}

?>