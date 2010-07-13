#!/usr/bin/env php
<?php
/**
 * Entry point of the application
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

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