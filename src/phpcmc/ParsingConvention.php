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
	const CODE    = 0;
	const CONTENT = 1;

	private $skipTokenCodes = array(
		T_COMMENT, T_WHITESPACE
	);

	/**
	 * Collects the PHP classes from a file
	 *
	 * @param SplFileInfo $file the file
	 *
	 * @return array
	 */
	public function collectPhpClassesFrom(SplFileInfo $file)
	{
		$lint = shell_exec(sprintf(
			'php -l %s 2>%s',
			$file->getPathname(),
			$this->isWindows() ? 'NUL' : '/dev/null'
		));

		if (false === strpos($lint, 'No syntax errors detected')) {
			return array();
		}

		$code   = file_get_contents($file->getPathname());
		$tokens = token_get_all($code);

		if (empty($tokens)) {
			return array();
		}

		$result = array();

		foreach ($tokens as $index => $token) {
			if (T_CLASS === $token[self::CODE]) {

				$className = $this->findClassName($tokens, $index + 1);
				if (null !== $className) {
					$result[] = $className;
				}
			}
		}

		return $result;
	}

	private function isWindows()
	{
		return 0 === strpos(strtolower(PHP_OS), 'win');
	}

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