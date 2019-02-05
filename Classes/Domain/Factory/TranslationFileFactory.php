<?php
namespace Lightwerk\L10nTranslator\Domain\Factory;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use Lightwerk\L10nTranslator\Domain\Model\Search;
use Lightwerk\L10nTranslator\Domain\Model\TranslationFile;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileFactory implements SingletonInterface
{
    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     */
    protected $l10nConfiguration;

    /**
     * @var \TYPO3\CMS\Core\Localization\LocalizationFactory
     */
    protected $localizationFactory;

    /**
     * @param \Lightwerk\L10nTranslator\Configuration\L10nConfiguration $l10nConfiguration
     * @return void
     */
    public function injectL10nConfiguration(\Lightwerk\L10nTranslator\Configuration\L10nConfiguration $l10nConfiguration)
    {
        $this->l10nConfiguration = $l10nConfiguration;
    }

    /**
     * @param \TYPO3\CMS\Core\Localization\LocalizationFactory $localizationFactory
     * @return void
     */
    public function injectLocalizationFactory(\TYPO3\CMS\Core\Localization\LocalizationFactory $localizationFactory)
    {
        $this->localizationFactory = $localizationFactory;
    }
    
    /**
     * @param string $relativePath
     * @return TranslationFile
     * @throws Exception
     */
    public function findByRelativePath($relativePath)
    {
        $splFileInfo = new \SplFileInfo(Environment::getExtensionsPath() . DIRECTORY_SEPARATOR . $relativePath);
        if ($splFileInfo->isFile() === false) {
            throw new Exception('Cannot create splFileInfo with path ' . $relativePath, 1466093531);
        }
        $translationFile = new TranslationFile();
        $translationFile->initFileSystem($splFileInfo, $this->l10nConfiguration->getAvailableL10nLanguages(), $this->localizationFactory);
        return $translationFile;
    }

    /**
     * @param string $path
     * @return TranslationFile
     * @throws Exception
     */
    public function findByPath($path)
    {
        try {
            $translationFile = $this->findByRelativePath($path);
        } catch (Exception $e) {
            $splFileInfo = new \SplFileInfo($path);
            if ($splFileInfo->isFile() === false) {
                throw new Exception('cannot create splFileInfo with path ' . $path, 1466093537);
            }
            $translationFile = new TranslationFile();
            $translationFile->initFileSystem($splFileInfo, $this->l10nConfiguration->getAvailableL10nLanguages(), $this->localizationFactory);
        }
        return $translationFile;
    }


    /**
     * @param Search $search
     * @return TranslationFile[]
     * @throws \Lightwerk\L10nTranslator\Domain\Model\Exception
     */
    public function findBySearch(Search $search)
    {
        $translationFiles = [];
        $languages = $search->hasLanguage() ? [$search->getLanguage()] : $this->l10nConfiguration->getAvailableL10nLanguages();
        $availableL10nFiles = $search->hasL10nFile() ? [$search->getL10nFile()] : $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($availableL10nFiles as $availableL10nFile) {
            $path = Environment::getExtensionsPath() . DIRECTORY_SEPARATOR . $availableL10nFile;
            $translationFile = new TranslationFile();
            $translationFile->initFileSystem(new \SplFileInfo($path), $languages, $this->localizationFactory);
            $translationFile->applySearch($search);
            $translationFiles[] = $translationFile;
        }
        return $translationFiles;
    }
}
