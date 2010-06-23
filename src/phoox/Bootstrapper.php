<?php
/**
 * Holds the Bootstrapper class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Contains common bootstrapping logic to be used by PHPUnit tests
 */
class Bootstrapper
{
	/**
	 * Bootstrap functionality for PHPUnit tests
	 *
	 * Sets up autoloaders.
	 *
	 * @param array  $library list of source paths
	 * @param string $file    overridable parameter used by classloaders
	 *
	 * @return LoaderSession
	 */
	public static function bootstrap(array $library, $file=__file__)
	{
		require_once dirname(__file__) . '/library.php';
		$loaderSession = new LoaderSession(new FileIncludeHandler());

		spl_autoload_register(array($loaderSession, 'start'));
		spl_autoload_register(array($loaderSession, 'stop'));

		foreach ($library as $dir) {
			$loaderSession->append(new DirLoader($loaderSession, $dir, $file));
		}

		return $loaderSession;
	}
}

?>