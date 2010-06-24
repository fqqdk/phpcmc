<?php
/**
 * Holds the LoaderSession class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of LoaderSession
 */
class LoaderSession {
	private $handler;
	private $classes = array();
	private $loaders = array();

	public function __construct(FileIncludeHandler $handler) {
		$this->handler = $handler;
	}

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

	public function remove(ClassLoader $loader)
	{
		spl_autoload_unregister(array($loader, 'load'));
	}

	public function start($className) {
		array_push($this->classes, $className);
		return false;
	}

	public function success() {
		$cl = array_pop($this->classes);
		$this->classes = array();
		$this->handler->restore();
	}

	public function stop($className) {
		$message  = 'Failed loading ' . $className . PHP_EOL;
		$message .= 'Loading order was : ' . PHP_EOL;
		foreach ($this->classes as $index => $class) {
			$message .= $index . ': ' . $class . PHP_EOL;
		}
		$message .= 'Loaders currently on the stack: ' . PHP_EOL;
		foreach ($this->loaders as $index => $loader) {
			$message .= $index . ': '. get_class($loader) . PHP_EOL;
			if ($loader instanceof DirLoader) {
				$message .= "\t\t created in file: " . $loader->getCreated() . PHP_EOL;
				$message .= "\t\t on directory: "    . $loader->getDir()     . PHP_EOL;
			}
		}
		$this->classes = array();
		$this->handler->restore();
		trigger_error($message);
	}

	public function classExists($className) {
		return class_exists($className, false) || interface_exists($className, false);
	}

	public function includeClassFile($fileName) {
		$this->handler->startIncluding($fileName);
		include_once $fileName;
		$this->handler->stopIncluding($fileName);
	}
}

?>