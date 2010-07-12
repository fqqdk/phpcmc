<?php
/**
 * Holds the ParsingConvention class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of ParsingConvention
 */
class ParsingConvention implements PhpCmcNamingConvention
{
	/**
	 * @final integer index of the token code in a token array
	 */
	const CODE = 0;

	/**
	 * @final integer index of the token content in a token array
	 */
	const CONTENT = 1;

	/**
	 * @var array Tokens to be expected after a class token, that precede the classname
	 */
	private $skipTokenCodes = array(T_COMMENT, T_WHITESPACE);

	/**
	 * @var PhpLinter the linter
	 */
	private $linter;

	/**
	 * Constructor
	 *
	 * @param PhpLinter $linter the linter
	 *
	 * @return ParsingConvention
	 */
	public function  __construct(PhpLinter $linter)
	{
		$this->linter = $linter;
	}

	/**
	 * Collects the PHP classes from a file
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return array
	 */
	public function collectPhpClassesFrom(SplFileInfo $file)
	{
		if (false == $this->linter->checkSyntax($file)) {
			return array();
		}

		$code   = file_get_contents($file->getPathname());
		$tokens = token_get_all($code);

		if (empty($tokens)) {
			return array();
		}

		$result = array();

		foreach ($tokens as $index => $token) {
			if (T_CLASS === $token[self::CODE] || T_INTERFACE === $token[self::CODE]) {

				$className = $this->findClassName($tokens, $index + 1);
				if (null !== $className) {
					$result[] = $className;
				}
			}
		}

		return $result;
	}

	/**
	 * Finds the classname after the class token has been detected
	 *
	 * @param array   $tokens the token stream
	 * @param integer $index  the index from which the searching should start
	 *
	 * @return string the classname or null if none has been found
	 */
	private function findClassName(array $tokens, $index)
	{
		for ($i = $index; $i < count($tokens); ++$i) {
			if (T_STRING === $tokens[$i][self::CODE]) {
				return $tokens[$i][self::CONTENT];
			}

			if (in_array($tokens[$i][self::CODE], $this->skipTokenCodes, true)) {
				continue;
			}

			return null;
		}

		return null;
	}
}

?>