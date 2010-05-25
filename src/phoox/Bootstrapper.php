<?php
/**
 * Holds the Bootstrapper class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of Bootstrapper
 */
class Bootstrapper
{
	public static function bootstrap(array $library)
	{
		require_once dirname(__file__) . '/library.php';
		$loaderSession = new LoaderSession(new FileIncludeHandler());

		spl_autoload_register(array($loaderSession, 'start'));
		foreach ($library as $dir) {
			spl_autoload_register(array(new DirLoader($loaderSession, $dir), 'load'));
		}
		spl_autoload_register(array($loaderSession, 'stop'));
	}
}

?>