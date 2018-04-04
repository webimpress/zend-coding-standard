<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Formatting;

use ZendCodingStandardTest\Sniffs\TestCase;

class ReturnTypeUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        if ($testFile === 'ReturnTypeUnitTest.1.inc') {
            return [
                6 => 1,
                7 => 3,
                9 => 2,
                10 => 2,
                17 => 2,
                18 => 3,
                20 => 1,
                21 => 2,
                22 => 1,
            ];
        }

        return [
            8 => 1,
            12 => 1,
            30 => 2,
            39 => 1,
            45 => 2,
            53 => 2,
            58 => 2,
            59 => 2,
            60 => 2,
            61 => 3,
            62 => 3,
            63 => 2,
            64 => 1,
            65 => 2,
            66 => 1,
            67 => 2,
            68 => 1,
            69 => 2,
            70 => 1,
            71 => 2,
            72 => 1,
            73 => 2,
            74 => 1,
            75 => 2,
            76 => 1,
            77 => 2,
            78 => 1,
            80 => 2,
            81 => 1,
            83 => 1,
            84 => 3,
            89 => 1,
            92 => 1,
            94 => 1,
            96 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
