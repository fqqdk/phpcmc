<?php
/**
 * Holds the Jabbar.php class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of Jabbar.php
 */
class Jabbar
{
	/**
	 * The entry point of the application
	 *
	 * @param array $argv arguments passed on the command line
	 *
	 * @return void
	 */
	public static function main(array $argv)
	{
		require_once 'phpcmc/PhpCmcApplication.php';
		PhpCmcApplication::bootstrap();

		$output = new OutputStream();
		$error  = new OutputStream(STDERR);
		$app    = new self($output, $error);

		$app->run($argv);
	}

	/**
	 * Runs the application
	 *
	 * @param array $argv arguments passed on the command line
	 *
	 * @return void
	 */
	public function run(array $argv)
	{
		$xml = new XMLWriter();
		$xml->openMemory();

		$builder = new PearXmlBuilder($xml);

		$builder->process(new RecursiveDirectoryWalker($argv[1]));

		echo $this->prettifyXmlString($xml->flush());
	}

	/**
	 * Prettifies the xml output
	 *
	 * @param string $string the xml string
	 *
	 * @return string
	 */
	public static function prettifyXmlString($string)
	{
		$string = preg_replace('/>\s*</','>'."\n".'<',$string);

		$xmlArray = explode("\n",$string);

		$currIndent = 1;

		$string = array_shift($xmlArray) . "\n";

		foreach ($xmlArray as $element) {
			if (preg_match('/^<([\w])+[^>\/]*>$/U',$element)) {
				$string .=  str_repeat("\t", $currIndent) . $element . "\n";
				$currIndent ++;
			} else if ( preg_match('/^<\/.+>$/',$element)) {
				$currIndent --;
				$string .=  str_repeat("\t", $currIndent) . $element . "\n";
			} else {
				$string .=  str_repeat("\t", $currIndent) . $element . "\n";
			}
		}

		return $string;

	}
}

?>