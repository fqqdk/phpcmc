<?php
/**
 * Holds the ScriptBuilder class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Generic builder class for creating shell commands to run
 */
class ShellCommandBuilder
{
	/**
	 * @var string $executable the executable to run
	 */
	private $executable;

	/**
	 * @var boolean $isWindowsBinary whether the executable is a windows binary
	 */
	private $isWindowsBinary;

	/**
	 * @var array $args arguments to pass to the script
	 */
	protected $args = array();

	/**
	 * Creates commandbuilder for binary executable based commands
	 *
	 * @param string $executable the executable
	 *
	 * @return ShellCommandBuilder
	 */
	public static function newBinary($executable)
	{
		return new self($executable, true);
	}

	/**
	 * Creates commandbuilder for commands based on a script
	 *
	 * @param string $executable the executable
	 *
	 * @return ShellCommandBuilder
	 */
	public static function newScript($executable)
	{
		return new self($executable, false);
	}

	/**
	 * Creates commandbuilder specifically suitable for PHP commands
	 *
	 * @return PhpCommandBuilder
	 */
	public static function newPhp()
	{
		return new PhpCommandBuilder();
	}

	/**
	 * Constructor
	 *
	 * @param string  $executable      the executable the command is based on
	 * @param boolean $isWindowsBinary whether the executable is a windows binary
	 */
	protected function __construct($executable, $isWindowsBinary)
	{
		$this->executable      = $executable;
		$this->isWindowsBinary = $isWindowsBinary;
	}

	/**
	 * Adds an argument for the script
	 *
	 * @param string $arg the argument
	 *
	 * @return ShellCommandBuilder
	 */
	public function addArg($arg)
	{
		$this->args[] = $arg;

		return $this;
	}

	/**
	 * Adds multiple arguments for the script
	 *
	 * @param array $args the list of arguments
	 *
	 * @return ShellCommandBuilder
	 */
	public function addArgs(array $args)
	{
		foreach ($args as $arg) {
			$this->addArg($arg);
		}

		return $this;
	}

	/**
	 * Adds a property to the script
	 *
	 * @param string $propertyName  the name of the property
	 * @param string $propertyValue the value of the property
	 * @param string $separator     the separator
	 *
	 * @return ShellCommandBuilder
	 */
	public function addProperty($propertyName, $propertyValue, $separator='=')
	{
		$this->args[] = $propertyName . $separator . $propertyValue;

		return $this;
	}

	/**
	 * Adds multiple properties for the script
	 *
	 * @param array  $properties map of properties
	 * @param string $separator  the separator
	 *
	 * @return ShellCommandBuilder
	 */
	public function addProperties(array $properties, $separator='=')
	{
	    if ($separator == ' ') {
	        throw new InvalidArgumentException('use separate args instead');
		}

		foreach ($properties as $propertyName => $propertyValue) {
			$this->addProperty($propertyName, $propertyValue, $separator);
		}

		return $this;
	}

	/**
	 * Runs the command
	 *
	 * @param ShellCommandRunner $runner runner facility
	 * @param string             $stdin  contents that should be passed as stdin
	 * @param array              $env    environment variables
	 *
	 * @return string the output of the command
	 */
	public function runWith(ShellCommandRunner $runner, $stdin='', $env=array())
	{
		$shellCommand = $this->executable . ' ' . implode(' ', array_map(
			array($this, 'escapeArg'), $this->args
		));

		return $runner->run($shellCommand, $stdin, $env, $this->isWindowsBinary);
	}

	/**
	 * Checks whether the underlying OS is windows or not
	 *
	 * @return boolean
	 */
	protected function isWindows()
	{
	    return false !== strpos(strtolower(PHP_OS), 'win');
	}

	/**
	 * Checks whether the underlying OS is linux
	 *
	 * @return boolean
	 */
	protected function isLinux()
	{
		return false !== strpos(strtolower(PHP_OS), 'linux');
	}

	/**
	 * Escapes a shell argument
	 *
	 * @param string $arg the argument to escape
	 *
	 * @return string
	 */
	protected function escapeArg($arg)
	{
		if ('\\' === substr($arg, -1, 1)) {
			$arg .= '\\';
		}

		return escapeshellarg($arg);
	}
}

?>