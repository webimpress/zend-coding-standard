<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class DeclareStrictTypesUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'DeclareStrictTypesUnitTest.1.inc':
                return [1 => 1];
            case 'DeclareStrictTypesUnitTest.2.inc':
                return [2 => 1];
            case 'DeclareStrictTypesUnitTest.3.inc':
                return [8 => 1];
            case 'DeclareStrictTypesUnitTest.4.inc':
                return [1 => 1];
            case 'DeclareStrictTypesUnitTest.5.inc':
                return [2 => 2];
            case 'DeclareStrictTypesUnitTest.6.inc':
                return [1 => 2];
            case 'DeclareStrictTypesUnitTest.7.inc':
                return [
                    1 => 1,
                    6 => 1,
                ];
            case 'DeclareStrictTypesUnitTest.8.inc':
                return [6 => 1];
            case 'DeclareStrictTypesUnitTest.9.inc':
                return [1 => 2];
            case 'DeclareStrictTypesUnitTest.10.inc':
                return [
                    1 => 1,
                    4 => 1,
                ];
            case 'DeclareStrictTypesUnitTest.11.inc':
                return [
                    1 => 1,
                    5 => 1,
                ];
            case 'DeclareStrictTypesUnitTest.12.inc':
                return [3 => 2];
            case 'DeclareStrictTypesUnitTest.13.inc':
                return [2 => 2];
        }

        return [
            1 => 1,
            5 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
