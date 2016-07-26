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

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class Translation
{
    /**
     * @var string
     */
    protected $translationSource = '';

    /**
     * @var string
     */
    protected $translationTarget = '';
    
    /**
     * @var string
     */
    protected $translationKey = '';
    
    /**
     * @var string
     */
    protected $path = '';

    /**
     * @param string $path
     * @param string $translationKey
     * @param string $translationTarget
     * @param string $translationSource
     */
    public function __construct($path, $translationKey, $translationTarget, $translationSource = '')
    {
        $this->path = $path;
        $this->translationKey = $translationKey;
        $this->translationSource = $translationSource;
        $this->translationTarget = $translationTarget;
    }

    /**
     * @param Search $search
     * @return bool
     */
    protected function exactMatchSearch(Search $search)
    {
        $searchString = $search->getSearchString();
        if ($this->getTranslationTarget() === $searchString) {
            return true;
        }
        if ($search->getIncludeSource() === true) {
            return $this->getTranslationSource() === $searchString;
        }
        return false;
    }

    /**
     * @param Search $search
     * @return bool
     */
    protected function caseInSensitiveMatchSearch(Search $search)
    {
        $searchString = $search->getSearchString();
        if (strpos(strtolower($this->getTranslationTarget()), strtolower($searchString)) !== false) {
            return true;
        }
        if ($search->getIncludeSource() === true) {
            return strpos(strtolower($this->getTranslationSource()), strtolower($searchString)) !== false;
        }
        return false;
    }

    /**
     * @param Search $search
     * @return bool
     */
    protected function caseSensitiveMatchSearch(Search $search)
    {
        $searchString = $search->getSearchString();
        if (strpos($this->getTranslationTarget(), $searchString) !== false) {
            return true;
        }
        if ($search->getIncludeSource() === true) {
            return strpos($this->getTranslationSource(), $searchString) !== false;
        }
        return false;
    }

    /**
     * @param Search $search
     * @return bool
     */
    public function matchSearch(Search $search)
    {
        if ($search->getExactMatch() === true) {
            return $this->exactMatchSearch($search);
        }
        if ($search->getCaseSensitive() === true) {
            return $this->caseSensitiveMatchSearch($search);
        }
        return $this->caseInSensitiveMatchSearch($search);
    }

    /**
     * @param Translation $translation
     * @return void
     */
    public function replaceTranslationTargetByOtherTranslation(Translation $translation)
    {
        $this->translationTarget = $translation->getTranslationTarget();
    }

    /**
     * @param Translation $translation
     * @return void
     */
    public function replaceTranslationSourceByOtherTranslation(Translation $translation)
    {
        $this->translationSource = $translation->getTranslationSource();
    }
    
    /**
     * @return string $translationValue
     */
    public function getTranslationTarget()
    {
        return $this->translationTarget;
    }

    /**
     * @return string $translationValue
     */
    public function getTranslationSource()
    {
        return $this->translationSource;
    }

    
    /**
     * @return string $translationKey
     */
    public function getTranslationKey()
    {
        return $this->translationKey;
    }
    
    /**
     * @return string $path
     */
    public function getPath()
    {
        return $this->path;
    }
}
