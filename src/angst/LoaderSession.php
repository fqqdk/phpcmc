<?php
/**
 * Holds the LoaderSession class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * LoaderSession is a container of class loaders.
 *
 * Its purpose is to provide a framework for classloaders that may emit errors
 * during attempts to load classes which should be catched and to provide a way
 * to prevent PHP fatal errors from occuring by emitting user errors when
 * loading of a class failed.
 */
class LoaderSession {
	/**
	 * @var FileIncludeHandler the special error handler to use
	 */
	private $handler;

	/**
	 *
	 * @var array the stack of classes that are being loaded currently
	 */
	private $classes = array();

	/**
	 * @var array the list of loaders currently registered
	 */
	private $loaders = array();

	/**
	 * Constructor
	 *
	 * @param FileIncludeHandler $handler special error handler
	 *
	 * @return LoaderSession
	 */
	public function __construct(FileIncludeHandler $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * Appends a ClassLoader to the loader queue
	 *
	 * @param ClassLoader $loader the loader
	 *
	 * @return void
	 * @throws Exception
	 */
	public function append(ClassLoader $loader)
	{
		$this->loaders []= $loader;
		if (false == empty($this->classes)) {
			throw new Exception('Cannot add loader, a session is running.');
		}

		spl_autoload_unregister(array($this, 'stop'));
		spl_autoload_register(array($loader, 'load'));
		spl_autoload_register(array($this, 'stop'));
	}

	/**
	 * Removes a ClassLoader from the loader queue
	 *
	 * @param ClassLoader $loader the loader
	 *
	 * @return void
	 */
	public function remove(ClassLoader $loader)
	{
		spl_autoload_unregister(array($loader, 'load'));
	}

	/**
	 * This class autoloader function that is the very first on the queue
	 *
	 * When this method is called it tells the this LoaderSession that
	 * the system is trying to load a class
	 *
	 * @param string $className the classname that is being loaded
	 *
	 * @return boolean
	 */
	public function start($className)
	{
		array_push($this->classes, $className);
		return false;
	}

	/**
	 * This message tells this LoaderSession that the class has been loaded
	 * successfully.
	 *
	 * @return void
	 */
	public function success()
	{
		$this->classes = array();
		$this->handler->restore();
	}

	/**
	 * The very last classloader function registered.
	 *
	 * When this method is called that means that none of the
	 * registered ClassLoaders succeeded to load the class
	 *
	 * @param string $className the name of the class
	 *
	 * @return boolean
	 */
	public function stop($className)
	{
		$message  = 'Failed loading ' . $className . PHP_EOL;
		$message .= 'Loading order was : ' . PHP_EOL;
		foreach ($this->classes as $index => $class) {
			$message .= $index . ': ' . $class . PHP_EOL;
		}
		$message .= 'Loaders currently on the stack: ' . PHP_EOL;
		foreach ($this->loaders as $index => $loader) {
			$message .= $index . ': '. get_class($loader) . PHP_EOL;
			if ($loader instanceof DirLoader) {
				$message .= "\t\t" . 'created in file: ' . $loader->getCreated() . PHP_EOL;
				$message .= "\t\t" . 'on directory: '    . $loader->getDir()     . PHP_EOL;
			}
		}
		$this->classes = array();
		$this->handler->restore();
		trigger_error($message);
		return false;
	}

	/**
	 * Checks that a class or interface is already loaded.
	 *
	 * @param string $className the name of the class
	 *
	 * @return boolean
	 */
	public function classExists($className)
	{
		return class_exists($className, false) || interface_exists($className, false);
	}

	/**
	 * Attempts to include a file assumed to contain a class.
	 *
	 * @param string $fileName the file name
	 *
	 * @return void
	 */
	public function includeClassFile($fileName)
	{
		$this->handler->startIncluding($fileName);
		include_once $fileName;
		$this->handler->stopIncluding($fileName);
	}

	/**
	 * Removes classloaders
	 *
	 * @return void
	 */
	public function destroy()
	{
		foreach ($this->loaders as $loader) {
			$this->remove($loader);
		}
	}
}

?>