#!/usr/bin/env php
<?php
/**
 * Entry point of the application
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

// include_path hack
if (0 === strpos('@php_dir@', '@php_dir')) {
	set_include_path(
		dirname(__file__) . PATH_SEPARATOR . get_include_path()
	);
} else {
	set_include_path(
		'@php_dir@' . PATH_SEPARATOR . get_include_path()
	);
}

/**
 * Assuming the libraries are on the include_path.
 * We need the bootstrapper that sets the classloaders and the
 * CMC library. These will be packaged separately.
 */
require_once 'phpcmc/PhpCmcApplication.php';

/**
 * @final the current version of the application
 * @global
 */
define('PHPCMC_VERSION', '@package_version@');

PhpCmcApplication::main($_SERVER['argv']);

?>