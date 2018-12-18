<?php

namespace Lightwerk\L10nTranslator\Tests\Functional\Domain\Service;

/***************************************************************
 *  Copyright notice
 *  (c) 2016 Achim Fritz <af@lightwerk.com>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Lightwerk\L10nTranslator\Domain\Service\TranslationFileService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileServiceTest extends FunctionalTestCase
{
    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileService
     */
    protected $translationFileService;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/l10n_translator',
        'typo3conf/ext/l10n_translator/Tests/Fixtures/Extensions/demo'
    ];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['extbase', 'fluid', 'backend'];

    /**
     * @var string
     */
    protected $l10nDeFolder = '';

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->l10nDeFolder = Environment::getLabelsPath() . '/de/demo/Resources/Private/Language';
        if (is_dir($this->l10nDeFolder) === false) {
            mkdir($this->l10nDeFolder, 0777, true);
        }

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->translationFileService = $objectManager->get(TranslationFileService::class);
    }


    /**
     * @return void
     * @test
     */
    public function xml2XlfByDefaultCreatesXlfFile()
    {
        $xlfFile = 'demo/Resources/Private/Language/locallang.xlf';
        $content = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3locallang>
	<meta type="array">
		<type>database</type>
		<description>description</description>
	</meta>
	<data type="array">
		<languageKey index="de" type="array">
			<label index="search-start">translation string</label>
		</languageKey>
	</data>
</T3locallang>';
        file_put_contents($this->l10nDeFolder . '/de.locallang.xml', $content);
        $gmDate = gmdate('Y-m-d\TH:i:s\Z');
        $expected = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
        <file source-language="en" target-language="de" datatype="plaintext" original="messages" date="' . $gmDate . '" product-name="demo">
                <header/>
                <body>
                        <trans-unit id="search-start">
                                <source>orig string</source>
                                <target>translation string</target>
                        </trans-unit>
                </body>
        </file>
</xliff>';

        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, 'de');
        $this->assertSame(true, file_exists($this->l10nDeFolder . '/de.locallang.xlf'));
        $content = file_get_contents($this->l10nDeFolder . '/de.locallang.xlf');
        $this->assertXmlStringEqualsXmlString($expected, $content);
    }

    /**
     * @return void
     * @test
     */
    public function xml2XlfByDefaultCreatesXlfFileWithoutEmptyLables()
    {
        $xlfFile = 'demo/Resources/Private/Language/locallang1.xlf';
        $content = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3locallang>
	<meta type="array">
		<type>database</type>
		<description>description</description>
	</meta>
	<data type="array">
		<languageKey index="de" type="array">
		</languageKey>
	</data>
</T3locallang>';
        file_put_contents($this->l10nDeFolder . '/de.locallang1.xml', $content);
        $gmDate = gmdate('Y-m-d\TH:i:s\Z');
        $expected = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
        <file source-language="en" target-language="de" datatype="plaintext" original="messages" date="' . $gmDate . '" product-name="demo">
                <header/>
                <body>';
        $expected .= "\n\t\t</body>
        </file>
</xliff>";

        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, 'de', false);
        $this->assertSame(true, file_exists($this->l10nDeFolder . '/de.locallang1.xlf'));
        $content = file_get_contents($this->l10nDeFolder . '/de.locallang1.xlf');
        $this->assertXmlStringEqualsXmlString($expected, $content);
    }

    /**
     * @return void
     * @test
     */
    public function xml2XlfByDefaultCreatesXlfFileWithEmptyLables()
    {
        $xlfFile = 'demo/Resources/Private/Language/locallang2.xlf';
        $content = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3locallang>
	<meta type="array">
		<type>database</type>
		<description>description</description>
	</meta>
	<data type="array">
		<languageKey index="de" type="array">
		</languageKey>
	</data>
</T3locallang>';
        file_put_contents($this->l10nDeFolder . '/de.locallang2.xml', $content);
        $gmDate = gmdate('Y-m-d\TH:i:s\Z');
        $expected = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
        <file source-language="en" target-language="de" datatype="plaintext" original="messages" date="' . $gmDate . '" product-name="demo">
                <header/>
                <body>
                <trans-unit id="search-start">
                                <source>orig string</source>
                                <target>orig string</target>
                        </trans-unit>
                </body>
        </file>
</xliff>';

        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, 'de', true);
        $this->assertSame(true, file_exists($this->l10nDeFolder . '/de.locallang2.xlf'));
        $content = file_get_contents($this->l10nDeFolder . '/de.locallang2.xlf');
        $this->assertXmlStringEqualsXmlString($expected, $content);
    }

    /**
     * @return void
     * @test
     */
    public function xml2XlfCreatesXlfFile()
    {
        $xmlTranslationFile = 'demo/Resources/Private/Language/test.xml';
        $content = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3locallang>
	<meta type="array">
		<type>database</type>
		<description>description</description>
	</meta>
	<data type="array">
		<languageKey index="de" type="array">
			<label index="search-start">translation string</label>
		</languageKey>
	</data>
</T3locallang>';
        file_put_contents($this->l10nDeFolder . '/de.test.xml', $content);
        $gmDate = gmdate('Y-m-d\TH:i:s\Z');
        $expected = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
        <file source-language="en" target-language="de" datatype="plaintext" original="messages" date="' . $gmDate . '" product-name="demo">
                <header/>
                <body>
                        <trans-unit id="search-start">
                                <source>translation string</source>
                                <target>translation string</target>
                        </trans-unit>
                </body>
        </file>
</xliff>';
        $this->translationFileService->xml2Xlf($xmlTranslationFile, 'de');
        $this->assertSame(true, file_exists($this->l10nDeFolder . '/de.test.xlf'));
        $content = file_get_contents($this->l10nDeFolder . '/de.test.xlf');
        $this->assertXmlStringEqualsXmlString($expected, $content);
    }
}
