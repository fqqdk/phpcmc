<?php
/**
 * Holds the VarExportFormatter.php class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of VarExportFormatter.php
 */
class VarExportFormatter implements OutputFormatter
{
	private $baseDir;
	private $prefix;

	public function __construct($baseDir, $prefix)
	{
		$this->baseDir = $baseDir;
		$this->prefix  = $prefix;
	}

	public function header()
	{
		return '<?php return array(' . PHP_EOL;
	}

	public function classEntry($file, $className)
	{
		return sprintf(
			'"%s" => "%s",'.PHP_EOL,
			$className,
			$this->prefix . $this->getRelativeDirectory($file)
		);
	}

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
	private function getRelativeDirectory(SplFileInfo $file)
	{
		$result = $file->getPathname();
		$result = str_replace($this->baseDir, '', $result);
		$result = str_replace('\\', '/', $result);

		return $result;
	}
}

?>