<?php
namespace Lightwerk\L10nTranslator\Tests\Unit\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Lightwerk\L10nTranslator\Utility\StringUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Testcase for class \Lightwerk\L10nTranslator\StringUtility
 *
 * @package TYPO3
 * @subpackage l10n_translator
 */
class StringUtilityTest extends UnitTestCase
{

    /**
     * @dataProvider stripPathToLanguageFileDataProvider
     * @param string $fullPath
     * @param string $expectation
     * @return void
     * @test
     */
    public function stripPathToLanguageFile($fullPath, $expectation)
    {
        $this->assertSame($expectation, StringUtility::stripPathToLanguageFile($fullPath));
    }

    /**
     * DataProvider for stripPathToLanguageFile
     *
     * @return array
     */
    public function stripPathToLanguageFileDataProvider()
    {
        return [
            'Strips off path to private resources' => [
                'news/Resources/Private/Language/locallang.xlf',
                'news (locallang.xlf)'
            ],
            'Strips off path to pi1' => [
                'old_ext/pi1/locallang.xml',
                'old_ext (locallang.xml)'
            ],
            'Returns string that does not match' => [
                'my_ext/path/to/locallang.xlf',
                'my_ext/path/to/locallang.xlf'
            ]
        ];
    }
}
