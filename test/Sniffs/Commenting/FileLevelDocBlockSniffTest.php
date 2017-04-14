<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\CodingStandard\Sniffs\Commenting;

use ZendTest\CodingStandard\SniffTestCase;

class FileLevelDocBlockSniffTest extends SniffTestCase
{
    /**
     * @dataProvider assetsProvider
     */
    public function testAssets($asset, $fixed, $errorCount, array $errors, $warningCount, array $warnings)
    {
        $result = $this->processAsset($asset);

        $this->assertErrorCount($errorCount, $result);
        $this->assertWarningCount($warningCount, $result);

        foreach ($errors as $error) {
            $this->assertHasError($error, $result);
        }

        foreach ($warnings as $warning) {
            $this->assertHasWarning($warning, $result);
        }

        $this->assertAssetCanBeFixed($fixed, $result);
    }

    public function assetsProvider()
    {
        return [
            'FileLevelDocBlockPass' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockPass.php',
                'fixed'        => null,
                'errorCount'   => 0,
                'errors'       => [],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockMissing' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockMissing.php',
                'fixed'        => null,
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.Missing',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockSpacingAfterOpen' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockSpacingAfterOpen.php',
                'fixed'        => null,
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.SpacingAfterOpen',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockIncorrectSourceLink' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockIncorrectSourceLink.php',
                'fixed'        => __DIR__ . '/TestAsset/FileLevelDocBlockIncorrectSourceLink.fixed.php',
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectSourceLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockIncorrectCopyrightLink' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockIncorrectCopyrightLink.php',
                'fixed'        => __DIR__ . '/TestAsset/FileLevelDocBlockIncorrectCopyrightLink.fixed.php',
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectCopyrightLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockIncorrectLicenseLink' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockIncorrectLicenseLink.php',
                'fixed'        => __DIR__ . '/TestAsset/FileLevelDocBlockIncorrectLicenseLink.fixed.php',
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectLicenseLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockEmptyTags' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockEmptyTags.php',
                'fixed'        => null,
                'errorCount'   => 3,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.EmptySeeTag',
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.EmptyCopyrightTag',
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.EmptyLicenseTag',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockDeprecatedLinkTag' => [
                'asset'        => __DIR__ . '/TestAsset/FileLevelDocBlockDeprecatedLinkTag.php',
                'fixed'        => __DIR__ . '/TestAsset/FileLevelDocBlockDeprecatedLinkTag.fixed.php',
                'errorCount'   => 2,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.DeprecatedLinkTag',
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectSourceLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],
        ];
    }
}
