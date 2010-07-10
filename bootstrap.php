<?php
/**
 * PHPUnit bootstrap file
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Bootstrap function that encapsulates bootstrap behaviour
 *
 * @return void
 */
function __bootstrap()
{
	error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

	define('BASE_DIR' ,      dirname(__file__) . '/');
	define('BOOTSTRAP_FILE', __file__);
	define('SRC_DIR',        BASE_DIR . 'src/phpcmc/');
	define('ZETSUBO_DIR',    BASE_DIR . 'src/zetsubo/');
	define('ANGST_DIR',      BASE_DIR . 'src/angst/');
	define('TEST_DIR',       BASE_DIR . 'tests/');
	define('WORK_DIR',       BASE_DIR . 'build/work/');
	define('PHPCMC_SCRIPT',  BASE_DIR . 'src/phpcmc.php');

	require_once ANGST_DIR . 'Bootstrapper.php';

	$library = array(SRC_DIR, ANGST_DIR, ZETSUBO_DIR, TEST_DIR);
	$session = Bootstrapper::bootstrap($library, __file__);

	register_shutdown_function('__shutdown');
}

/**
 * @todo implement
 */
function __shutdown()
{
	// we should be able to save the state of the test run so far and
	// automatically generate a remaining test suite etc etc
}

__bootstrap();

?>