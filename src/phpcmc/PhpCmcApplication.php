<?php
/**
 * Holds the PhpCmcApplication class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of PhpCmcApplication
 */
class PhpCmcApplication
{
	public static function library() {
		return array(dirname(__file__) . '/');
	}

	public static function main(array $argv) {
		echo 'phpcmc 0.0.1 by fqqdk' . PHP_EOL . PHP_EOL;

		$dir = $argv[1];

		$rec = new RecursiveDirectoryIterator($dir);
		$it  = new RecursiveIteratorIterator($rec);

		foreach ($it as $file) {
			$className = $file->getBaseName('.php');
			echo $className . ' ' . dirname($file->getPathname()) . PHP_EOL;
		}
	}
}

?>