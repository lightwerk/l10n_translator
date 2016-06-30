<?php
namespace Lightwerk\L10nTranslator\Domain\Service;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Achim Fritz <af@achimfritz.de>, Lightwerk GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Lightwerk\L10nTranslator\Domain\Model\Search;
use Lightwerk\L10nTranslator\Domain\Model\TranslationFile;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileService implements SingletonInterface
{

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     * @inject
     */
    protected $translationFileFactory;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService
     * @inject
     */
    protected $translationFileWriterService;

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     * @inject
     */
    protected $l10nConfiguration;

    /**
     * @param string $xlfFile
     * @param string $language
     * @param boolean $createEmptyLabels
     * @throws \Lightwerk\L10nTranslator\Domain\Factory\Exception
     * @throws Exception
     * @return void
     */
    public function xml2XlfByDefaultXlf($xlfFile, $language, $createEmptyLabels = true)
    {
        $translationFile = $this->translationFileFactory->findByPath($xlfFile);
        $this->mergeXmlIntoDefault($translationFile, $language, $createEmptyLabels);
    }

    /**
     * @param string $xlfFile
     * @param boolean $createEmptyLabels
     * @throws \Lightwerk\L10nTranslator\Domain\Factory\Exception
     * @throws Exception
     * @return void
     */
    public function allXml2XlfByDefaultXlf($xlfFile, $createEmptyLabels = true)
    {
        $translationFile = $this->translationFileFactory->findByPath($xlfFile);
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        foreach ($languages as $language) {
            $this->mergeXmlIntoDefault($translationFile, $language, $createEmptyLabels);
        }
    }

    /**
     * @param string $language
     * @param bool $copyLabels
     * @return void
     * @throws Exception
     */
    public function createMissingFiles($language, $copyLabels = true)
    {
        $search = new Search('', '', '');
        $translationFiles = $this->translationFileFactory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
            if ($l10nTranslationFile->getSplFileInfo()->isFile() === false) {
                if ($copyLabels === true) {
                    foreach ($translationFile->getTranslations() as $translation) {
                        $l10nTranslationFile->addTranslation($translation);
                    }
                }
                $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
            }
        }
    }

    /**
     * @param string $xlfFile
     * @param string $language
     * @param string $altLanguage
     * @return void
     * @throws Exception
     * @throws \Lightwerk\L10nTranslator\Domain\Factory\Exception
     * @throws \Lightwerk\L10nTranslator\Domain\Model\Exception
     */
    public function overwriteWithAltLanguage($xlfFile, $language, $altLanguage)
    {
        $translationFile = $this->translationFileFactory->findByPath($xlfFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $altL10nTranslationFile = $translationFile->getL10nTranslationFile($altLanguage);
        foreach ($altL10nTranslationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->getOwnTranslation($translation) === null) {
                $l10nTranslationFile->addTranslation($translation);
            } else {
                $l10nTranslationFile->replaceTranslationTarget($translation);
            }
        }
        $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
    }

    /**
     * @param TranslationFile $translationFile
     * @param string $language
     * @param boolean $createEmptyLabels
     * @return void
     */
    protected function mergeXmlIntoDefault(TranslationFile $translationFile, $language, $createEmptyLabels)
    {
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $translations = $translationFile->getTranslations();
        foreach ($translations as $translation) {
            if ($createEmptyLabels === true && $l10nTranslationFile->getOwnTranslation($translation) === null) {
                $l10nTranslationFile->addTranslation($translation);
            }
            $l10nTranslationFile->replaceTranslationSource($translation);
        }
        $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
    }

    /**
     * @param string $xmlFile
     * @param string $language
     * @throws \Lightwerk\L10nTranslator\Domain\Factory\Exception
     * @throws Exception
     * @return void
     */
    public function xml2Xlf($xmlFile, $language)
    {
        $translationFile = $this->translationFileFactory->findByRelativePath($xmlFile);
        if ($language !== 'default') {
            $this->translationFileWriterService->writeTranslationXlf($translationFile->getL10nTranslationFile($language));
        } else {
            $this->translationFileWriterService->writeTranslationXlf($translationFile);
        }
    }
}
