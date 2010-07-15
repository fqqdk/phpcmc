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
	private $stack = array();

	public function __construct(XMLWriter $xml)
	{
		$this->xml = $xml;
	}

	private function push(SplFileInfo $file)
	{
		array_push($this->stack, $file);
	}

	private function pop()
	{
		return array_pop($this->stack);
	}

	private function isEmpty()
	{
		return empty($this->stack);
	}

	private function top()
	{
		return $this->stack[count($this->stack) - 1];
	}

	private function log($message)
	{
		fwrite(STDERR, $message . PHP_EOL);
	}

	public function foundFile(SplFileInfo $file)
	{

		while ($this->isCurrentDirAdjacentTo($file)) {
			// this is the bug
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

	private function isCurrentDirAdjacentTo(SplFileInfo $file)
	{
		if ($this->isEmpty()) {
			return false;
		}

		return 0 !== strpos($file->getPathname(), $this->top()->getPathname());
	}

	public function start()
	{
		$this->xml->openMemory();
	}

	public function process($walker)
	{
		$walker->walk($this);
	}

	public function finish()
	{
		while (false == $this->isEmpty()) {
			$this->pop();
			$this->xml->endElement();
		}
	}
}

?>