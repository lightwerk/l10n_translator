<?php
namespace Lightwerk\L10nTranslator\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use TYPO3\CMS\Core\Localization\LocalizationFactory;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
abstract class AbstractTranslationFile
{
    /**
     * @var \splFileInfo
     */
    protected $splFileInfo = null;

    /**
     * @var string
     */
    protected $language = '';

    /**
     * @var string
     */
    protected $extension = '';

    /**
     * @var string
     */
    protected $relativePath = '';

    /**
     * @var Translation[]
     */
    protected $translations = [];

    /**
     * @var Translation[]
     */
    protected $matchedTranslations = [];


    /**
     * @return string
     */
    public function getCleanPath()
    {
        return str_replace('//', '/', $this->getSplFileInfo()->getPathname());
    }

    /**
     * @return array
     */
    public function translationsToArray()
    {
        $arr = [];
        foreach ($this->getTranslations() as $translation) {
            $arr[$translation->getTranslationKey()] = $translation->getTranslationTarget();
        }
        return $arr;
    }

    /**
     * @param LocalizationFactory $localizationFactory
     * @return void
     */
    protected function initTranslations(LocalizationFactory $localizationFactory)
    {
        $parsedData = $this->getParsedData($localizationFactory);
        foreach ($parsedData[$this->getLanguage()] as $key => $labels) {
            if (isset($labels[0]['source']) === true && isset($labels[0]['target']) === true) {
                $translation = new Translation($this->getCleanPath(), $key, $labels[0]['target'], $labels[0]['source']);
                $this->translations[] = $translation;
                $this->matchedTranslations[] = $translation;
            }
        }
    }

    /**
     * @param LocalizationFactory $localizationFactory
     * @return array
     */
    abstract protected function getParsedData(LocalizationFactory $localizationFactory);

    /**
     * @param Search $search
     * @return Translation[]
     */
    public function getTranslationsBySearch(Search $search)
    {
        $filtered = [];
        foreach ($this->getTranslations() as $translation) {
            if ($translation->matchSearch($search) === true) {
                $filtered[] = $translation;
            }
        }
        return $filtered;
    }

    /**
     * @param Search $search
     * @return bool
     */
    public function hasTranslationOfSearch(Search $search)
    {
        foreach ($this->getTranslations() as $translation) {
            if ($translation->matchSearch($search) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Translation $translation
     * @return void
     */
    public function replaceTranslationTarget(Translation $translation)
    {
        $replaced = [];
        $currentTranslations = $this->getTranslations();
        foreach ($currentTranslations as $currentTranslation) {
            if ($currentTranslation->getTranslationKey() === $translation->getTranslationKey()) {
                $currentTranslation->replaceTranslationTargetByOtherTranslation($translation);
            }
            $replaced[] = $currentTranslation;
        }
        $this->translations = $replaced;
    }

    /**
     * @param Translation $translation
     * @return void
     */
    public function replaceTranslationSource(Translation $translation)
    {
        $replaced = [];
        $currentTranslations = $this->getTranslations();
        foreach ($currentTranslations as $currentTranslation) {
            if ($currentTranslation->getTranslationKey() === $translation->getTranslationKey()) {
                $currentTranslation->replaceTranslationSourceByOtherTranslation($translation);
            }
            $replaced[] = $currentTranslation;
        }
        $this->translations = $replaced;
    }

    /**
     * @param Translation $translation
     * @return Translation|null
     */
    public function getOwnTranslation(Translation $translation)
    {
        foreach ($this->getTranslations() as $ownTranslation) {
            if ($translation->getTranslationKey() === $ownTranslation->getTranslationKey()) {
                return $ownTranslation;
            }
        }
        return null;
    }

    /**
     * @param Translation $translation
     * @return bool
     */
    public function hasOwnTranslation(Translation $translation)
    {
        return $this->getOwnTranslation($translation) !== null;
    }

    /**
     * @param Translation $translation
     * @return void
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations[] = $translation;
    }


    /**
     * @return \splFileInfo
     */
    public function getSplFileInfo()
    {
        return $this->splFileInfo;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return Translation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @return Translation[]
     */
    public function getMatchedTranslations()
    {
        return $this->matchedTranslations;
    }
}
