<?php
namespace Lightwerk\L10nTranslator\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Localization\LocalizationFactory;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFile extends AbstractTranslationFile
{

    /**
     * @var L10nTranslationFile[]
     */
    protected $l10nTranslationFiles = [];

    /**
     * @param \SplFileInfo $splFileInfo
     * @param array $languages
     * @param LocalizationFactory $localizationFactory
     * @return void
     * @throws Exception
     */
    public function initFileSystem(\SplFileInfo $splFileInfo, array $languages, LocalizationFactory $localizationFactory)
    {
        $this->splFileInfo = $splFileInfo;

        $pathPart = str_replace('/', '\/', Environment::getExtensionsPath() . DIRECTORY_SEPARATOR);
        $this->relativePath = preg_replace('/' . $pathPart . '/', '', $this->getCleanPath());
        $parts = explode(DIRECTORY_SEPARATOR, $this->relativePath);
        if (count($parts) < 1) {
            throw new Exception('Invalid file in ' . $this->splFileInfo->getRealPath(), 1466171558);
        }
        $this->language = 'default';
        $this->extension = $parts[0];
        $this->initTranslations($localizationFactory);
        foreach ($languages as $language) {
            $path = $this->getL10nTranslationFilePath($language);
            $splFileInfo = new \SplFileInfo($path);
            $l10nTranslationFile = new L10nTranslationFile($this);
            $l10nTranslationFile->initFileSystem($splFileInfo, $localizationFactory);
            $l10nTranslationFile->initMissingTranslations();
            $this->l10nTranslationFiles[$language] = $l10nTranslationFile;
        }
    }

    /**
     * @param LocalizationFactory $localizationFactory
     * @return array
     */
    protected function getParsedData(LocalizationFactory $localizationFactory)
    {
        return $localizationFactory->getParsedData($this->getCleanPath(), $this->getLanguage());
    }

    /**
     * @param string $language
     * @return L10nTranslationFile
     * @throws Exception
     */
    public function getL10nTranslationFile($language)
    {
        if (isset($this->l10nTranslationFiles[$language]) === false) {
            throw new Exception('l10nTranslationFile of language ' . $language . ' does not exist.', 1466587863);
        }
        return $this->l10nTranslationFiles[$language];
    }

    /**
     * @param string $language
     * @return string
     */
    public function getL10nTranslationFilePath($language)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $this->getRelativePath());
        array_pop($parts);
        $path = Environment::getLabelsPath() . DIRECTORY_SEPARATOR . $language;
        $path .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $language . '.' . $this->getSplFileInfo()->getBasename();
        return $path;
    }

    /**
     * @param Search $search
     * @return void
     */
    public function applySearch(Search $search)
    {
        foreach ($this->getL10nTranslationFiles() as $l10nTranslationFile) {
            $l10nTranslationFile->applySearch($search);
        }
    }

    /**
     * @return L10nTranslationFile[]
     */
    public function getL10nTranslationFiles()
    {
        return $this->l10nTranslationFiles;
    }
}
