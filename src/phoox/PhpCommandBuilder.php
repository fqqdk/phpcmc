<?php
/**
 * Holds the PhpCommandBuilder class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * PhpCommandBuilder is a utility that builds CLI php commands
 */
class PhpCommandBuilder extends ShellCommandBuilder
{
	/**
	 * @var array arguments to pass to the php CLI
	 */
	private $phpArgs = array();

	/**
	 * Constructor
	 *
	 * @param string $executable the PHP CLI executable
	 */
	public function __construct($executable='php')
	{
		parent::__construct($executable, false);
	}

	/**
	 * Adds an argument that should be passed to the PHP CLI
	 *
	 * @param string $arg the argument
	 *
	 * @return PhpCommandBuilder
	 */
	public function addPhpArg($arg)
	{
		$this->phpArgs[] = $arg;

		return $this;
	}

	/**
	 * Adds multiple arguments that will be passed to the PHP CLI
	 *
	 * @param array $args the list of arguments
	 *
	 * @return PhpCommandBuilder
	 */
	public function addPhpArgs(array $args)
	{
		foreach ($args as $arg) {
			$this->addPhpArg($arg);
		}

		return $this;
	}

	/**
	 * Adds a PHP property that will be passed to the PHP CLI
	 *
	 * @param string $propertyName  the name of the property
	 * @param string $propertyValue the value of the property
	 *
	 * @return PhpCommandBuilder
	 */
	public function addPhpProperty($propertyName, $propertyValue)
	{
		$this->phpArgs[] = $propertyName;
		$this->phpArgs[] = $propertyValue;

		return $this;
	}

	/**
	 * Adds multiple properties that will be passed to the PHP CLI
	 * 
	 * @param array $properties the map of properties
	 * 
	 * @return PhpCommandBuilder 
	 */
	public function addPhpProperties(array $properties)
	{
		foreach ($properties as $propertyName => $propertyValue) {
			$this->addPhpProperty($propertyName, $propertyValue);
		}

		return $this;
	}

	/**
	 * Adds a PHP ini variable
	 *
	 * @param string $iniVarName  the ini variable's name
	 * @param string $iniVarValue the value for the ini variable
	 *
	 * @return PhpCommandBuilder
	 */
	public function addIniVar($iniVarName, $iniVarValue)
	{
		if ($this->isWindows() && false !== strpos($iniVarValue, ';')) {
			$iniVarValue = '\'' . $iniVarValue . '\'';
		}

		if ($this->isLinux() && false !== strpos($iniVarValue, ';')) {
			$iniVarValue = '\'' . $iniVarValue . '\'';
		}

		$this->phpArgs[] = '-d';
		$this->phpArgs[] = $iniVarName.'='.$iniVarValue;

		return $this;
	}

	/**
	 * Adds multiple ini variables that will be passed to the PHP CLI
	 *
	 * @param array $iniVars the map of ini variables
	 *
	 * @return PhpCommandBuilder
	 */
	public function addIniVars(array $iniVars)
	{
		foreach ($iniVars as $iniVarName => $iniVarValue) {
			$this->addIniVar($iniVarName, $iniVarValue);
		}

		return $this;
	}

	/**
	 * Builds and runs the php command
	 *
	 * @param ShellCommandRunner $runner the runner facility
	 * @param string             $stdin  stdin contents
	 * @param array              $env    environment variables
	 *
	 * @return mixed
	 */
	public function runWith(ShellCommandRunner $runner, $stdin='', $env=array())
	{
		$this->args = array_merge($this->phpArgs, array('--'), $this->args);

		return parent::runWith($runner, $stdin, $env);
	}
}

?>