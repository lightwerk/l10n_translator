<?php
namespace Lightwerk\L10nTranslator\Command;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Lightwerk\L10nTranslator\Configuration\L10nConfiguration;
use Lightwerk\L10nTranslator\Domain\Model\Search;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class L10nCommandController extends CommandController
{
    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     */
    protected $translationFileFactory;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService
     */
    protected $translationFileWriterService;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileService
     */
    protected $translationFileService;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @param \TYPO3\CMS\Core\Cache\CacheManager $cacheManager
     * @return void
     */
    public function injectCacheManager(\TYPO3\CMS\Core\Cache\CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory $translationFileFactory
     * @return void
     */
    public function injectTranslationFileFactory(\Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory $translationFileFactory)
    {
        $this->translationFileFactory = $translationFileFactory;
    }

    /**
     * @param \Lightwerk\L10nTranslator\Domain\Service\TranslationFileService $translationFileService
     * @return void
     */
    public function injectTranslationFileService(\Lightwerk\L10nTranslator\Domain\Service\TranslationFileService $translationFileService)
    {
        $this->translationFileService = $translationFileService;
    }

    /**
     * @param \Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService $translationFileWriterService
     * @return void
     */
    public function injectTranslationFileWriterService(\Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService $translationFileWriterService)
    {
        $this->translationFileWriterService = $translationFileWriterService;
    }

    /**
     * @param string $xmlFile
     * @return void
     */
    public function prepareXmlLanguageFilesCommand($xmlFile)
    {
        $this->translationFileService->prepareXmlLanguageFiles($xmlFile);
    }

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
     * @param string $language
     * @return void
     */
    public function createSourceTagCommand($language)
    {
        $this->flushCache();
        $this->translationFileService->createSourceTagsForAllFiles($language);
        $this->flushCache();
    }

    /**
     * @return void
     */
    public function createAllSourceTagCommand()
    {
        $this->flushCache();
        $this->translationFileService->createSourceTagsForAllFilesAndLanguages();
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
     * @param string $xmlFile
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
     * @param string $xmlFile
     * @return void
     */
    public function allXml2XlfCommand($xmlFile)
    {
        $this->flushCache();
        $this->translationFileService->allXml2Xlf($xmlFile);
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

    /**
     * @param bool $copyLabels
     * @return void
     */
    public function createMissingFilesForAllSystemLanguagesCommand($copyLabels = true)
    {
        $this->flushCache();
        $languages = $this->getAllSystemLanguages();
        foreach ($languages as $language) {
            try {
                $this->translationFileService->createMissingFiles($language, $copyLabels);
            } catch (\Lightwerk\L10nTranslator\Domain\Model\Exception $e) {
                $this->outputLine($e->getMessage());
            }
        }
        $this->flushCache();
    }

    /**
     * @param string $sourceLanguage
     * @return void
     */
    public function createAllMissingLabelsForAllSystemLanguagesCommand($sourceLanguage = 'default')
    {
        $this->flushCache();
        $languages = $this->getAllSystemLanguages();
        foreach ($languages as $language) {
            try {
                $this->translationFileService->createAllMissingLabels($language, $sourceLanguage);
            } catch (\Lightwerk\L10nTranslator\Domain\Model\Exception $e) {
                $this->outputLine($e->getMessage());
            }
        }
        $this->flushCache();
    }

    /**
     * @param bool $copyLabels
     * @return void
     */
    public function createMissingFilesForAllConfiguredLanguagesCommand($copyLabels = true)
    {
        $this->flushCache();
        $languages = $this->getAllConfiguredLanguages();
        foreach ($languages as $language) {
            try {
                $this->translationFileService->createMissingFiles($language, $copyLabels);
            } catch (\Lightwerk\L10nTranslator\Domain\Model\Exception $e) {
                $this->outputLine($e->getMessage());
            }
        }
        $this->flushCache();
    }

    /**
     * @param string $sourceLanguage
     * @return void
     */
    public function createAllMissingLabelsForAllConfiguredLanguagesCommand($sourceLanguage = 'default')
    {
        $this->flushCache();
        $languages = $this->getAllConfiguredLanguages();
        foreach ($languages as $language) {
            try {
                $this->translationFileService->createAllMissingLabels($language, $sourceLanguage);
            } catch (\Lightwerk\L10nTranslator\Domain\Model\Exception $e) {
                $this->outputLine($e->getMessage());
            }
        }
        $this->flushCache();
    }

    /**
     * @return array
     */
    private function getAllSystemLanguages()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $rows = $queryBuilder->select('language_isocode')
            ->from('sys_language')
            ->execute()
            ->fetchAll();
        $rows = $rows ?: [];
        return array_unique(array_column($rows, 'language_isocode'));
    }

    /**
     * @return array
     */
    private function getAllConfiguredLanguages()
    {
        return GeneralUtility::makeInstance(L10nConfiguration::class)->getAvailableL10nLanguages();
    }
}
