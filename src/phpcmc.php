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
require_once 'phoox/Bootstrapper.php';
require_once 'phpcmc/PhpCmcApplication.php';

define('PHPCMC_VERSION', '@package_version@');
error_reporting(E_ALL);
Bootstrapper::bootstrap(PhpCmcApplication::library());
PhpCmcApplication::main($_SERVER['argv']);

?>