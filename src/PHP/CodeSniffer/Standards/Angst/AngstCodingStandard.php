<?php
/**
 * Holds the PHP_CodeSniffer_Standards_AngstCodingStandard class
 * 
 * @author fqqdk <fqqdk@freemail.hu>
 */

require_once 'PHP/CodeSniffer/Standards/Ustream/UstreamCodingStandard.php';

/**
 * Angst coding standard
 */
class   PHP_CodeSniffer_Standards_Angst_AngstCodingStandard
extends PHP_CodeSniffer_Standards_Ustream_UstreamCodingStandard
{
	/**
	 * The external sniffs to exclude
	 *
	 * @return array
	 */
	public function getExcludedSniffs()
	{
		return array(
			'Ustream/Sniffs/Documenting/RequireFileAuthorSniff.php'
		);
	}
}

?>