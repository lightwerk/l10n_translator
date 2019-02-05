<?php
namespace Lightwerk\L10nTranslator\Domain\Service;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Lightwerk\L10nTranslator\Domain\Model\Search;
use Lightwerk\L10nTranslator\Domain\Model\Translation;
use Lightwerk\L10nTranslator\Domain\Model\TranslationFile;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileService implements SingletonInterface
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
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     */
    protected $l10nConfiguration;

    /**
     * @param \Lightwerk\L10nTranslator\Configuration\L10nConfiguration $l10nConfiguration
     * @return void
     */
    public function injectL10nConfiguration(\Lightwerk\L10nTranslator\Configuration\L10nConfiguration $l10nConfiguration)
    {
        $this->l10nConfiguration = $l10nConfiguration;
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
     * @param TranslationFileWriterService $translationFileWriterService
     * @return void
     */
    public function injectTranslationFileWriterService(\Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService $translationFileWriterService)
    {
        $this->translationFileWriterService = $translationFileWriterService;
    }

    /**
     * @param string $xlfFile
     * @param string $language
     * @param boolean $createEmptyLabels
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
                        if ($l10nTranslationFile->hasOwnTranslation($translation)) {
                            $l10nTranslationFile->getOwnTranslation($translation)->replaceTranslationSourceByOtherTranslation($translation);
                            continue;
                        }
                        $l10nTranslationFile->addTranslation($translation);
                    }
                }
                $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
            }
        }
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @param string $sourceLanguage
     * @return void
     */
    public function overwriteWithLanguage($l10nFile, $language, $sourceLanguage)
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $sourceL10nTranslationFile = $translationFile->getL10nTranslationFile($sourceLanguage);
        foreach ($sourceL10nTranslationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                $l10nTranslationFile->addTranslation($translation);
            } else {
                $l10nTranslationFile->replaceTranslationTarget($translation);
            }
        }
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @param string $sourceLanguage
     * @return void
     */
    public function createMissingLabels($l10nFile, $language, $sourceLanguage = 'default')
    {
        if ($sourceLanguage !== 'default') {
            $l10nTranslationFile = $this->mergeMissingLabelsFromSourceLanguage($l10nFile, $language, $sourceLanguage);
        } else {
            $l10nTranslationFile = $this->mergeMissingLabelsFromDefaultLanguage($l10nFile, $language);
        }
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @param string $sourceLanguage
     */
    public function createMissingSourceTags($l10nFile, $language, $sourceLanguage = 'default')
    {
        $l10nTranslationFile = $this->mergeSourceTagFromDefaultLanguage($l10nFile, $language);
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
    }

    /**
     * @param string $language
     * @param string $sourceLanguage
     * @return void
     */
    public function createAllMissingLabels($language, $sourceLanguage = 'default')
    {
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($l10nFiles as $l10nFile) {
            $this->createMissingLabels($l10nFile, $language, $sourceLanguage);
        }
    }

    /**
     * @param string $language
     * @return void
     */
    public function createSourceTagsForAllFiles($language)
    {
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($l10nFiles as $l10nFile) {
            $this->createMissingSourceTags($l10nFile, $language);
        }
    }

    /**
     * @param string $language
     * @return void
     */
    public function createSourceTagsForAllFilesAndLanguages()
    {
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        foreach ($languages as $language) {
            $this->createSourceTagsForAllFiles($language);
        }
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @param string $sourceLanguage
     * @return \Lightwerk\L10nTranslator\Domain\Model\L10nTranslationFile
     */
    protected function mergeMissingLabelsFromSourceLanguage($l10nFile, $language, $sourceLanguage)
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $sourceL10nTranslationFile = $translationFile->getL10nTranslationFile($sourceLanguage);
        foreach ($translationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                $sourceTranslation = $sourceL10nTranslationFile->getOwnTranslation($translation);
                if ($sourceTranslation !== null) {
                    $l10nTranslationFile->addTranslation($sourceTranslation);
                } else {
                    $l10nTranslationFile->addTranslation($translation);
                }
            }
        }
        return $l10nTranslationFile;
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @return \Lightwerk\L10nTranslator\Domain\Model\L10nTranslationFile
     */
    protected function mergeMissingLabelsFromDefaultLanguage($l10nFile, $language)
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        foreach ($translationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                $l10nTranslationFile->addTranslation($translation);
            }
            $currentTranslation = $l10nTranslationFile->getOwnTranslation($translation);
            if (empty($currentTranslation->getTranslationSource())) {
                $currentTranslation->replaceTranslationSourceByOtherTranslation($translation);
            }
        }
        return $l10nTranslationFile;
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @return \Lightwerk\L10nTranslator\Domain\Model\L10nTranslationFile
     */
    protected function mergeSourceTagFromDefaultLanguage($l10nFile, $language)
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        foreach ($translationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation)) {
                $l10nTranslationFile->replaceTranslationSource($translation);
            }
        }
        return $l10nTranslationFile;
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

    /**
     * @param string $xmlFile
     * @return void
     */
    public function allXml2Xlf($xmlFile)
    {
        $translationFile = $this->translationFileFactory->findByRelativePath($xmlFile);
        $this->translationFileWriterService->writeTranslationXlf($translationFile);
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        foreach ($languages as $language) {
            $this->translationFileWriterService->writeTranslationXlf($translationFile->getL10nTranslationFile($language));
        }
    }

    /**
     * @param string $xmlFile
     * @return void
     * @throws Exception
     */
    public function prepareXmlLanguageFiles($xmlFile)
    {
        $translationFile = $this->translationFileFactory->findByRelativePath($xmlFile);
        $xmlPath = $translationFile->getCleanPath();
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        foreach ($languages as $language) {
            $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
            $splFileInfo = $l10nTranslationFile->getSplFileInfo();
            if ($splFileInfo->isFile() === true) {
                throw new Exception('l10n language file already exists ' . $splFileInfo->getPathname(), 1476776271);
            }
            $parent = $splFileInfo->getPathInfo();
            if ($parent->isDir() === false) {
                if (@mkdir($parent->getPathname(), 0777, true) === false) {
                    throw new Exception('cannot create directory ' . $parent->getPathname(), 1476776272);
                }
                GeneralUtility::fixPermissions($parent->getPathname());
            }
            if (@copy($xmlPath, $splFileInfo->getPathname()) === false) {
                throw new Exception('cannot copy ' . $xmlPath . ' to ' . $splFileInfo->getPathname(), 1476776273);
            }
            GeneralUtility::fixPermissions($splFileInfo->getPathname());
        }
    }

    /**
     * For all configured languages we update the source of the label from the POST request.
     * This is because it was changed in the default language which is the source of all
     * other languages.
     *
     * So changes in "default" are reflected in an updated source of all other languages.
     *
     * @param array $postParam
     */
    public function updateSourceInFiles(array $postParam)
    {
        $configuredLanguages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $translationFile = $this->translationFileFactory->findByPath($postParam['path']);
        $translation = new Translation('', $postParam['key'], '', $postParam['target']);

        foreach ($configuredLanguages as $language) {
            if ($language === 'default') {
                continue;
            }

            $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
            $l10nTranslationFile->replaceTranslationSource($translation);
            $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
        }
    }
}
