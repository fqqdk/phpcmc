<?php
/**
 * Holds the ScriptBuilder class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of ScriptBuilder
 */
class ShellCommandBuilder
{
	/**
	 * @var string $executable
	 */
	private $executable;

	/**
	 * @var boolean $isWindowsBinary
	 */
	private $isWindowsBinary;

	/**
	 * @var array $args to pass to the script
	 */
	protected $args = array();

	public static function newBinary($executable)
	{
		return new self($executable, true);
	}

	public static function newScript($executable)
	{
		return new self($executable, false);
	}

	/**
	 * @return PhpCommandBuilder
	 */
	public static function newPhp()
	{
		return new PhpCommandBuilder();
	}

	protected function __construct($executable, $isWindowsBinary)
	{
		$this->executable      = $executable;
		$this->isWindowsBinary = $isWindowsBinary;
	}

	public function addArg($arg)
	{
		$this->args[] = $arg;

		return $this;
	}

	public function addArgs(array $args)
	{
		foreach ($args as $arg) {
			$this->addArg($arg);
		}

		return $this;
	}

	public function addProperty($propertyName, $propertyValue, $separator='=')
	{
		$this->args[] = $propertyName . $separator . $propertyValue;

		return $this;
	}

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

	public function runWith(ShellCommandRunner $runner, $stdin='', $env=array())
	{
		$shellCommand = $this->executable . ' ' . implode(' ', array_map(
			array($this, 'escapeArg'), $this->args
		));

		return $runner->run($shellCommand, $stdin, $env, $this->isWindowsBinary);
	}

	protected function isWindows()
	{
	    return false !== strpos(strtolower(PHP_OS), 'win');
	}

	protected function isLinux()
	{
		return false !== strpos(strtolower(PHP_OS), 'linux');
	}

	protected function escapeArg($arg)
	{
		if ('\\' === substr($arg, -1, 1)) {
			$arg .= '\\';
		}

		return escapeshellarg($arg);
	}
}

?>