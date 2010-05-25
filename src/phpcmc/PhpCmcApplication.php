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

//		var_dump($argv);

		$dir = $argv[1];

		foreach (glob($dir . '/*.php') as $file) {
			$className = basename($file, '.php');
			echo $className . ' ' . dirname($file) . PHP_EOL;
		}
	}
}

?>