<?php
namespace Lightwerk\L10nTranslator\Domain\Model;

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
     * @return void
     * @throws Exception
     */
    protected function initTranslations(LocalizationFactory $localizationFactory)
    {
        $parsedData = $localizationFactory->getParsedData($this->getCleanPath(), $this->getLanguage());
        foreach ($parsedData[$this->getLanguage()] as $key => $labels) {
            if (isset($labels[0]['source']) === false) {
                throw new Exception('no source found ' . $this->getCleanPath(), 1466171554);
            }
            if (isset($labels[0]['target']) === false) {
                throw new Exception('no target found ' . $this->getCleanPath(), 1466171555);
            }
            $translation = new Translation($this->getCleanPath(), $key, $labels[0]['target'], $labels[0]['source']);
            $this->translations[] = $translation;
            $this->matchedTranslations[] = $translation;
        }
    }


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
