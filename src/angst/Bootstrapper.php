<?php
/**
 * Holds the Bootstrapper class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Contains common bootstrapping logic to be used by PHPUnit tests
 */
class Bootstrapper
{
	/**
	 * @var LoaderSession session of classloaders
	 */
	private static $loaderSession;

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
		$angstDir = dirname(__file__) . '/';

		require_once $angstDir . 'ClassLoader.php';
		require_once $angstDir . 'ErrorHandler.php';
		require_once $angstDir . 'LoaderSession.php';
		require_once $angstDir . 'FileIncludeHandler.php';
		require_once $angstDir . 'DirLoader.php';

		self::$loaderSession = new LoaderSession(new FileIncludeHandler());

		spl_autoload_register(array(self::$loaderSession, 'start'));
		spl_autoload_register(array(self::$loaderSession, 'stop'));

		foreach ($library as $dir) {
			self::$loaderSession->append(new DirLoader(self::$loaderSession, $dir, $file));
		}

		return self::$loaderSession;
	}

	/**
	 * Removes the registered classloaders from the test
	 *
	 * @return void
	 */
	public static function destroy()
	{
		self::$loaderSession->destroy();
		spl_autoload_unregister(array(self::$loaderSession, 'start'));
		spl_autoload_unregister(array(self::$loaderSession, 'stop'));
	}
}

?>