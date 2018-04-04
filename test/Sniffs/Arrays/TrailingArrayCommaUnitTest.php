<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Arrays;

use ZendCodingStandardTest\Sniffs\TestCase;

class TrailingArrayCommaUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            11 => 1,
            14 => 1,
            17 => 1,
            22 => 1,
            25 => 1,
            26 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
