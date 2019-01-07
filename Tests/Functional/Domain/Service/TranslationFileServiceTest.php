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
     * @var string
     */
    protected $l10nFrFolder = '';

    /**
     * @var string
     */
    protected $l10nItFolder = '';

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
        $this->l10nFrFolder = Environment::getLabelsPath() . '/fr/demo/Resources/Private/Language';
        if (is_dir($this->l10nFrFolder) === false) {
            mkdir($this->l10nFrFolder, 0777, true);
        }
        $this->l10nItFolder = Environment::getLabelsPath() . '/it/demo/Resources/Private/Language';
        if (is_dir($this->l10nItFolder) === false) {
            mkdir($this->l10nItFolder, 0777, true);
        }

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->translationFileService = $objectManager->get(TranslationFileService::class);
    }

    /**
     * @return void
     * @test
     */
    public function xml2XlfByDefaultCreatesXlfFileWithoutEmptyLables()
    {
        $xlfFile = 'demo/Resources/Private/Language/locallang1.xlf';
        $content = file_get_contents(__DIR__ . '/../../../Fixtures/Files/EmptyLabels.xml');
        file_put_contents($this->l10nDeFolder . '/de.locallang1.xml', $content);
        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'de'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/Empty.xlf')
        );

        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, 'de', false);
        $this->assertTrue(file_exists($this->l10nDeFolder . '/de.locallang1.xlf'));
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
        $content = file_get_contents(__DIR__ . '/../../../Fixtures/Files/EmptyLabels.xml');
        file_put_contents($this->l10nDeFolder . '/de.locallang2.xml', $content);

        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'de'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabelBothOrig.xlf')
        );

        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, 'de', true);
        $this->assertTrue(file_exists($this->l10nDeFolder . '/de.locallang2.xlf'));
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
        $content = file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabel.xml');
        file_put_contents($this->l10nDeFolder . '/de.test.xml', $content);
        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'de'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabelBothTranslated.xlf')
        );
        $this->translationFileService->xml2Xlf($xmlTranslationFile, 'de');
        $this->assertTrue(file_exists($this->l10nDeFolder . '/de.test.xlf'));
        $content = file_get_contents($this->l10nDeFolder . '/de.test.xlf');
        $this->assertXmlStringEqualsXmlString($expected, $content);
    }

    /**
     * @return void
     * @test
     */
    public function createMissingFilesCreatesFilesWithSourceAndTargetWithTargetOnlyInput()
    {
        $this->translationFileService->createMissingFiles('fr');
        $this->assertTrue(file_exists($this->l10nFrFolder . '/fr.locallang.xlf'));
        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'fr'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabelTranslated.xlf')
        );
        $this->assertXmlStringEqualsXmlString($expected, file_get_contents($this->l10nFrFolder . '/fr.locallang.xlf'));
    }

    /**
     * @return void
     * @test
     */
    public function createMissingFilesCreatesFilesWithSourceAndTargetWithTargetAndSourceInput()
    {
        $this->translationFileService->createMissingFiles('it');
        $this->assertTrue(file_exists($this->l10nItFolder . '/it.locallang.xlf'));
        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'it'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabelTranslated.xlf')
        );
        $this->assertXmlStringEqualsXmlString($expected, file_get_contents($this->l10nItFolder . '/it.locallang.xlf'));
    }

    /**
     * @return void
     * @test
     */
    public function createAllMissingLabelsCreatesFilesWithSourceAndTargetWithTargetOnlyInput()
    {
        $this->translationFileService->createAllMissingLabels('fr');
        $this->assertTrue(file_exists($this->l10nFrFolder . '/fr.locallang.xlf'));
        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'fr'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabelTranslated.xlf')
        );
        $this->assertXmlStringEqualsXmlString($expected, file_get_contents($this->l10nFrFolder . '/fr.locallang.xlf'));
    }

    /**
     * @return void
     * @test
     */
    public function createAllMissingLabelsCreatesFilesWithSourceAndTargetWithTargetAndSourceInput()
    {
        $this->translationFileService->createAllMissingLabels('it');
        $this->assertTrue(file_exists($this->l10nItFolder . '/it.locallang.xlf'));
        $expected = str_replace(
            ['###DATE###', '###LANGUAGE###'],
            [gmdate('Y-m-d\TH:i:s\Z'), 'it'],
            file_get_contents(__DIR__ . '/../../../Fixtures/Files/OneLabelTranslated.xlf')
        );
        $this->assertXmlStringEqualsXmlString($expected, file_get_contents($this->l10nItFolder . '/it.locallang.xlf'));
    }
}
