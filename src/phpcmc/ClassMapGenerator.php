<?php
/**
 * Holds the ClassMapGenerator class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ClassMapGenerator
 */
class ClassMapGenerator
{
	private $error;

	public function  __construct(PhpCmcErrorListener $error)
	{
		$this->error = $error;
	}

	public function generateClassMap(Traversable $it, PhpCmcNamingConvention $naming, $dir)
	{
		$result = array();
		foreach ($it as $file) {
			$classes = $naming->collectPhpClassesFrom($file);

			foreach ($classes as $className) {
				$location = $this->getClassDirectory($dir, $file);
				if (isset($result[$className])) {
					$message = sprintf(
						'Duplicate class %s in %s, first defined in %s',
						$className, $result[$className], $location
					);
					$this->error->error($message);
				}
				$result[$className] = $location;
			}
		}
		return $result;
	}


	/**
	 * Callculates the directory string that should be displayed for a class entry
	 *
	 * @param string      $dir  the base source directory
	 * @param SplFileInfo $file the class file
	 *
	 * @return string
	 */
	private function getClassDirectory($dir, SplFileInfo $file)
	{
		$result = $file->getPathname();
		$result = str_replace($dir, '', $result);
		$result = str_replace('\\', '/', $result);

		return $result;
	}
}

?>