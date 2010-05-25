<?php
/**
 * 
 */

require_once dirname(__file__) . '/../../../src/phoox/Bootstrapper.php';
Bootstrapper::bootstrap(array(
	dirname(__file__) . '/lib1/',
	dirname(__file__) . '/lib2/'
));

new FirstClass;

function foo($code, $message)
{
	if (false !== strpos($message, 'Failed loading NonExistant')) {
		exit (0);
	}
}

set_error_handler('foo');
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
new NonExistant;

exit(1);

?>