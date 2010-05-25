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

	public static function main(array $args) {
		echo 'phpcmc 0.0.1 by fqqdk' . PHP_EOL . PHP_EOL;
	}
}

?>