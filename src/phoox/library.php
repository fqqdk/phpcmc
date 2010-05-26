<?php
/**
 * Bootstrapper library
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

interface ClassLoader {
	public function load($className);
}

interface ErrorHandler {
	public function handle($errorCode, $errorMessage);
}

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

class DirLoader implements ClassLoader
{
	private $session;
	private $dir;
	private $created;

	public function __construct(LoaderSession $session, $dir, $created) {
		$this->session = $session;
		$this->dir     = $dir;
		$this->created = $created;
	}

	public function load($className) {
		$this->session->includeClassFile($this->dir . $className . '.php');

		$success = $this->session->classExists($className);
		if ($success) {
			$this->session->success();
		}
		return $success;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function getCreated()
	{
		return $this->created;
	}
}

class FileIncludeHandler implements ErrorHandler {
	private $files = array();

	private function fileName() {
		return $this->files[count($this->files) - 1];
	}

	public function startIncluding($fileName) {
		$thisHandler = array($this, 'handle');
		
		$oldHandler  = set_error_handler($thisHandler);
		if ($oldHandler === $thisHandler) {
			restore_error_handler();
		}
		array_push($this->files, $fileName);
	}

	/**
	 *
	 * @param <type> $fileName
	 *
	 * @todo what if an included file tampers with the errorhandling?
	 *
	 * @return void
	 */
	public function stopIncluding($fileName) {
		array_pop($this->files);
		restore_error_handler();
	}

	public function restore() {
		$this->files      = array();
	}

	private function shouldSwallow($errorCode) {
		return (bool) ($errorCode & error_reporting());
	}

	private function isOwnFileIncludeWarning($errorMessage) {
		if (empty($this->files)) {
			return false;
		}

		return (bool) preg_match('#'.preg_quote($this->fileName(), '#').'#', $errorMessage);
	}

	/**
	 *
	 * @param <type> $errorCode
	 * @param <type> $errorMessage
	 *
	 * @todo check shouldswallow method
	 *
	 * @return <type>
	 */
	public function handle($errorCode, $errorMessage) {
		if (false === $this->shouldSwallow($errorCode)) {
			return false;
		}
		return $this->isOwnFileIncludeWarning($errorMessage);
	}
}

?>