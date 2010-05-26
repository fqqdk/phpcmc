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