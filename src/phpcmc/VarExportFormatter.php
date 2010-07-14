<?php
/**
 * Holds the VarExportFormatter.php class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of VarExportFormatter.php
 */
class VarExportFormatter implements OutputFormatter
{
	/**
	 * @var string base directory to strip off the file paths
	 */
	private $baseDir;

	/**
	 * @var string prefix to add to the beginning of the paths
	 */
	private $prefix;

	/**
	 * Constructor
	 *
	 * @param string $baseDir base directory to strip off the file paths
	 * @param string $prefix  prefix to add to the beginning of the paths
	 */
	public function __construct($baseDir, $prefix)
	{
		$this->baseDir = $baseDir;
		$this->prefix  = $prefix;
	}

	/**
	 * The header of the output
	 *
	 * @return string
	 */
	public function header()
	{
		return '<?php return array(' . PHP_EOL;
	}

	/**
	 * One formatted class entry
	 *
	 * @param string $className the class
	 * @param string $file      the file
	 *
	 * @return string
	 */
	public function classEntry($className, $file)
	{
		return sprintf(
			'"%s" => "%s",'.PHP_EOL,
			$className,
			$this->prefix . $this->getRelativeDirectory($file)
		);
	}

	/**
	 * The footer of the output
	 *
	 * @return string
	 */
	public function footer()
	{
		return '); ?'.'>';
	}

	/**
	 * Callculates the directory string that should be displayed for a class entry
	 *
	 * @param SplFileInfo $file the class file
	 *
	 * @return string
	 */
	private function getRelativeDirectory($file)
	{
		$result = $file;
		$result = str_replace($this->baseDir, '', $result);
		$result = str_replace('\\', '/', $result);

		return $result;
	}
}

?>