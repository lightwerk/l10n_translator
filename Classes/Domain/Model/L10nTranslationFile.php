<?php
namespace Lightwerk\L10nTranslator\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Achim Fritz <af@lightwerk.com>, Lightwerk GmbH
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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Localization\LocalizationFactory;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class L10nTranslationFile extends AbstractTranslationFile
{


    /**
     * @var TranslationFile
     */
    protected $translationFile;

    /**
     * @var Translation[]
     */
    protected $missingTranslations = [];

    /**
     * @var Translation[]
     */
    protected $matchedMissingTranslations = [];

    /**
     * @param TranslationFile $translationFile
     * @throws Exception
     */
    public function __construct(TranslationFile $translationFile)
    {
        $this->translationFile = $translationFile;
    }

    /**
     * @param \SplFileInfo $splFileInfo
     * @param LocalizationFactory $localizationFactory
     * @return void
     * @throws Exception
     */
    public function initFileSystem(\SplFileInfo $splFileInfo, LocalizationFactory $localizationFactory)
    {
        $this->splFileInfo = $splFileInfo;
        $pathPart = str_replace('/', '\/', Environment::getLabelsPath() . DIRECTORY_SEPARATOR);
        $this->relativePath = preg_replace('/' . $pathPart . '/', '', $this->getCleanPath());
        $parts = explode(DIRECTORY_SEPARATOR, $this->relativePath);
        if (count($parts) < 2) {
            throw new Exception('Invalid file in ' . $this->splFileInfo->getRealPath(), 1466171553);
        }
        $this->language = $parts[0];
        $this->extension = $parts[1];
        $this->initTranslations($localizationFactory);
    }

    /**
     * @param LocalizationFactory $localizationFactory
     * @return array
     */
    protected function getParsedData(LocalizationFactory $localizationFactory)
    {
        if ($this->getSplFileInfo()->isFile() === true) {
            return $localizationFactory->getParsedData($this->getCleanPath(), $this->getLanguage());
        }
        return $localizationFactory->getParsedData($this->getTranslationFile()->getCleanPath(), $this->getLanguage());
    }

    /**
     * @return void
     */
    public function initMissingTranslations()
    {
        foreach ($this->translationFile->getTranslations() as $translation) {
            if ($this->hasOwnTranslation($translation) === false) {
                $this->missingTranslations[] = new Translation($translation->getPath(), $translation->getTranslationKey() , '', $translation->getTranslationSource());
            }
        }
    }

    /**
     * @return Translation[]
     */
    public function getMissingTranslations()
    {
        return $this->missingTranslations;
    }

    /**
     * @return Translation[]
     */
    public function getMatchedMissingTranslations()
    {
        return $this->matchedMissingTranslations;
    }


    /**
     * @param Search $search
     * @return void
     */
    public function applySearch(Search $search)
    {
        if ($search->hasSearchString() === true) {
            $this->matchedTranslations = $this->getTranslationsBySearch($search);
        } else {
            $this->matchedTranslations = $this->getTranslations();
        }
        $this->matchedMissingTranslations = $this->getMissingTranslationsBySearch($search);
    }

    /**
     * @param Search $search
     * @return Translation[]
     */
    protected function getMissingTranslationsBySearch($search)
    {
        $filtered = [];
        if ($search->getIncludeSource() === true) {
            if ($search->hasSearchString() === true) {
                foreach ($this->getMissingTranslations() as $translation) {
                    if ($translation->matchSearch($search) === true) {
                        $filtered[] = $translation;
                    }
                }
            } else {
                $filtered = $this->getMissingTranslations();
            }
        }
        return $filtered;
    }
    
    /**
     * @return TranslationFile
     */
    public function getTranslationFile()
    {
        return $this->translationFile;
    }

    /**
     * @param Translation $translation
     * @return void
     * @throws Exception
     */
    public function upsertTranslationTarget(Translation $translation)
    {
        if ($this->hasOwnTranslation($translation) === true) {
            $this->replaceTranslationTarget($translation);
        } elseif ($this->translationFile->hasOwnTranslation($translation) === true) {
            $clonedTranslation = clone($this->translationFile->getOwnTranslation($translation));
            $clonedTranslation->replaceTranslationTargetByOtherTranslation($translation);
            $this->addTranslation($clonedTranslation);
        } else {
            throw new Exception('cannot upsert translation ' . $translation->getTranslationKey(), 1469774422);
        }
    }
}
