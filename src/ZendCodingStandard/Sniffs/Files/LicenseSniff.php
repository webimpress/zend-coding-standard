<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

use Zend\CodingStandard\Utils\LicenseUtils;

class ZendCodingStandard_Sniffs_Files_LicenseSniff implements \PHP_CodeSniffer_Sniff
{
    private $licenseFile;

    public function __construct()
    {
        $this->licenseFile = LicenseUtils::getLicenseFile();
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     * @see    Tokens.php
     */
    public function register()
    {
        return [T_OPEN_TAG];
    }

    /**
     * Called when one of the token types that this sniff is listening for is
     * found.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPtr  The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // Skip all files except the license file
        if (substr($phpcsFile->getFilename(), -10) !== $this->licenseFile->getFilename()) {
            return ($phpcsFile->numTokens + 1);
        }

        if (! $this->licenseFile->getRealPath()) {
            $error = 'Missing LICENSE.md file in the component root dir';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingLicense');
            if ($fix === true) {
                LicenseUtils::createLicenseFile();
            }

            // Ignore the rest of the file.
            return ($phpcsFile->numTokens + 1);
        }

        // Get copyright year
        list($firstYear, $lastYear) = LicenseUtils::getCopyrightDate($this->licenseFile);
        if (! $lastYear) {
            $lastYear = $firstYear;
        }

        // Check copyright year
        if ($lastYear !== gmdate('Y')) {
            $error = 'Invalid copyright date in COPYING.md';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'InvalidCopyrightDate');
            if ($fix === true) {
                LicenseUtils::updateCopyright($this->licenseFile, $firstYear);
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);
    }
}
