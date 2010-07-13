<?php
/**
 * PHPUnit bootstrap file
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Bootstrap function that encapsulates bootstrap behaviour
 *
 * @global
 *
 * @return void
 */
function __bootstrap()
{
	error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

	/**
	 * @final base directory of the package
	 */
	define('BASE_DIR' ,       dirname(__file__) . '/');

	/**
	 * @final filename of this bootstrap file
	 */
	define('BOOTSTRAP_FILE',  __file__);

	/**
	 * @final source directory of the application
	 */
	define('SRC_DIR',         BASE_DIR . 'src/phpcmc/');

	/**
	 * @final source directory of the Zetsubo framework
	 */
	define('ZETSUBO_DIR',     BASE_DIR . 'src/zetsubo/');

	/**
	 * @final source directory of the Angst framework
	 */
	define('ANGST_DIR',       BASE_DIR . 'src/angst/');

	/**
	 * @final source directory of the common test helpers for the application
	 */
	define('TEST_HELPER_DIR', BASE_DIR . 'tests/phpcmc/helpers/');

	/**
	 * @final working directory of the package
	 */
	define('WORK_DIR',        BASE_DIR . 'build/work/');

	/**
	 * @final the script serving as the entry point of the application
	 */
	define('PHPCMC_SCRIPT',   BASE_DIR . 'src/phpcmc.php');

	require_once ANGST_DIR . 'Bootstrapper.php';

	$library = array(SRC_DIR, ANGST_DIR, ZETSUBO_DIR, TEST_HELPER_DIR);
	$session = Bootstrapper::bootstrap($library, __file__);

	register_shutdown_function('__shutdown');
}

/**
 * Shutdown function
 *
 * @todo implement
 *
 * @global
 *
 * @return void
 */
function __shutdown()
{
	// we should be able to save the state of the test run so far and
	// automatically generate a remaining test suite etc etc
}

__bootstrap();

?>