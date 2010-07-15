<?php
/**
 * Holds the PearXmlBuilder class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of PearXmlBuilder
 */
class PearXmlBuilder implements FileWalkListener
{
	/**
	 * @var XmlWriter the xml
	 */
	private $xml;

	/**
	 * @var array directory stack
	 */
	private $stack = array();

	/**
	 * Constructor
	 *
	 * @param XMLWriter $xml xml writer
	 *
	 * @return PearXmlBuilder
	 */
	public function __construct(XMLWriter $xml)
	{
		$this->xml = $xml;
	}

	/**
	 * Pushes directory on the stack
	 *
	 * @param SplFileInfo $file the directory
	 *
	 * @return void
	 */
	private function push(SplFileInfo $file)
	{
		array_push($this->stack, $file);
	}

	/**
	 * Pops a directory from the stack
	 *
	 * @return SplFileInfo
	 */
	private function pop()
	{
		return array_pop($this->stack);
	}

	/**
	 * Checks that the stack is empty or not
	 *
	 * @return boolean
	 */
	private function isEmpty()
	{
		return empty($this->stack);
	}

	/**
	 * Gets the top directory on the stack
	 *
	 * @return SplFileInfo
	 */
	private function top()
	{
		return $this->stack[count($this->stack) - 1];
	}

	/**
	 * This event is fired when a FileWalker finds a file
	 *
	 * @param SplFileInfo $file the found file
	 *
	 * @return void
	 */
	public function foundFile(SplFileInfo $file)
	{
		while (false == $this->isUnderCurrentDir($file)) {
			$this->pop();
			$this->xml->endElement();
		}

		if ($file->isDir()) {
			$this->push($file);
			$this->xml->startElement('dir');
			$this->xml->writeAttribute('name', $file->getFilename());
			return;
		}

		$this->xml->startElement('file');
		$this->xml->writeAttribute('name', $file->getFilename());
		$this->xml->writeAttribute('role', 'php');
		$this->xml->endElement();
	}

	/**
	 * Checks that a file is under the current directory on the stack
	 *
	 * @param SplFileInfo $file the file that is checked
	 *
	 * @return boolean
	 */
	private function isUnderCurrentDir(SplFileInfo $file)
	{
		if ($this->isEmpty()) {
			return true;
		}

		return 0 === strpos($file->getPathname(), $this->top()->getPathname());
	}

	/**
	 * Walks the given file walker and adds found files to the xml
	 *
	 * @param FileWalker $walker the walker
	 *
	 * @return void
	 */
	public function process(FileWalker $walker)
	{
		$walker->walk($this);
		while (false == $this->isEmpty()) {
			$this->pop();
			$this->xml->endElement();
		}
	}
}

?>