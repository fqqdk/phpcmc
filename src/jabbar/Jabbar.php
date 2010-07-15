<?php
/**
 * Holds the Jabbar.php class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of Jabbar.php
 */
class Jabbar
{
	public static function main(array $argv)
	{
		require_once 'phpcmc/PhpCmcApplication.php';
		PhpCmcApplication::bootstrap();

		$output = new OutputStream();
		$error  = new OutputStream(STDERR);
		$app    = new self($output, $error);

		$app->run($argv);
	}

	public function run(array $argv)
	{
		$xml = new XMLWriter();

		$builder = new PearXmlBuilder($xml);

		$builder->start();
		$builder->process(new RecursiveDirectoryWalker($argv[1]));
		$builder->finish();

		echo $this->prettifyXmlString($xml->flush());
	}

    public function prettifyXmlString($string)
    {
        /**
         * put each element on it's own line
         */
        $string =preg_replace("/>\s*</",">\n<",$string);

        /**
         * each element to own array
         */
        $xmlArray = explode("\n",$string);

        /**
         * holds indentation
         */
        $currIndent = 1;

        /**
         * set xml element first by shifting of initial element
         */
        $string = array_shift($xmlArray) . "\n";

        foreach($xmlArray as $element) {
            /** find open only tags... add name to stack, and print to string
             * increment currIndent
             */

            if (preg_match('/^<([\w])+[^>\/]*>$/U',$element)) {
                $string .=  str_repeat("\t", $currIndent) . $element . "\n";
                $currIndent ++;
            }

            /**
             * find standalone closures, decrement currindent, print to string
             */
            elseif ( preg_match('/^<\/.+>$/',$element)) {
                $currIndent --;
                $string .=  str_repeat("\t", $currIndent) . $element . "\n";
            }
            /**
             * find open/closed tags on the same line print to string
             */
            else {
                $string .=  str_repeat("\t", $currIndent) . $element . "\n";
            }
        }

        return $string;

    }
}

?>