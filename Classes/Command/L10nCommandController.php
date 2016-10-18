<?php
namespace Lightwerk\L10nTranslator\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Achim Fritz <af@lightwerk.com>, Lightwerk
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
use TYPO3\CMS\Extbase\MVC\Controller\CommandController;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class L10nCommandController extends CommandController
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
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileService
     * @inject
     */
    protected $translationFileService;

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     * @inject
     */
    protected $l10nConfiguration;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     * @inject
     */
    protected $cacheManager;

    /**
     * @param string $xlfFile
     * @param boolean $createEmptyLabels
     * @return void
     */
    public function allXml2XlfByDefaultXlfCommand($xlfFile, $createEmptyLabels = false)
    {
        $this->flushCache();
        $this->translationFileService->allXml2XlfByDefaultXlf($xlfFile, $createEmptyLabels);
        $this->flushCache();
    }

    /**
     * @param string $xlfFile
     * @param string $language
     * @param boolean $createEmptyLabels
     * @return void
     */
    public function xml2XlfByDefaultXlfCommand($xlfFile, $language, $createEmptyLabels = false)
    {
        $this->flushCache();
        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, $language, $createEmptyLabels);
        $this->flushCache();
    }

    /**
     * @param string $language
     * @param bool $copyLabels
     * @return void
     */
    public function createMissingFilesCommand($language, $copyLabels = true)
    {
        $this->flushCache();
        $this->translationFileService->createMissingFiles($language, $copyLabels);
        $this->flushCache();
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @param string $sourceLanguage
     * @return void
     */
    public function overwriteWithLanguageCommand($l10nFile, $language, $sourceLanguage)
    {
        $this->flushCache();
        $this->translationFileService->overwriteWithLanguage($l10nFile, $language, $sourceLanguage);
        $this->flushCache();
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @param string $sourceLanguage
     * @return void
     */
    public function createMissingLabelsCommand($l10nFile, $language, $sourceLanguage = 'default')
    {
        $this->flushCache();
        $this->translationFileService->createMissingLabels($l10nFile, $language, $sourceLanguage);
        $this->flushCache();
    }

    /**
     * @param string $language
     * @param string $sourceLanguage
     * @return void
     */
    public function createAllMissingLabelsCommand($language, $sourceLanguage = 'default')
    {
        $this->flushCache();
        $this->translationFileService->createAllMissingLabels($language, $sourceLanguage);
        $this->flushCache();
    }

    /**
     * @return void
     */
    public function flushCacheCommand()
    {
        $this->flushCache();
    }

    /**
     * @param string $searchString
     * @param string $language
     * @param string $l10nFile
     * @param bool $caseSensitive
     * @param bool $exactMatch
     * @param bool $includeSource
     * @return void
     */
    public function listCommand($searchString = '', $language = '', $l10nFile = '', $caseSensitive = false, $exactMatch = false, $includeSource = false)
    {
        $search = new Search($searchString, $language, $l10nFile, $caseSensitive, $exactMatch, $includeSource);
        $translationFiles = $this->translationFileFactory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                $translations = $l10nTranslationFile->getMatchedTranslations();
                foreach ($translations as $translation) {
                    $this->outputLine($l10nTranslationFile->getLanguage() . ' ' . $translation->getTranslationKey() . ': ' . $translation->getTranslationTarget());
                }
                $translations = $l10nTranslationFile->getMatchedMissingTranslations();
                foreach ($translations as $translation) {
                    $this->outputLine($l10nTranslationFile->getLanguage() . ' ' . $translation->getTranslationKey() . ': ' . $translation->getTranslationTarget());
                }
            }
        }
    }

    /**
     * @param string $xlfFile
     * @param string $language
     * @return void
     */
    public function xml2XlfCommand($xmlFile, $language = 'default')
    {
        $this->flushCache();
        $this->translationFileService->xml2Xlf($xmlFile, $language);
        $this->flushCache();
    }

    /**
     * @param string $xlfFile
     * @return void
     */
    public function allXml2XlfCommand($xmlFile)
    {
        $this->flushCache();
        $this->translationFileService->allXml2XlfCommand($xmlFile);
        $this->flushCache();
    }

    /**
     * @return void
     */
    protected function flushCache()
    {
        $cacheFrontend = $this->cacheManager->getCache('l10n');
        $cacheFrontend->flush();
    }
}
