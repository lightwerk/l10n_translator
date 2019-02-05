<?php
namespace Lightwerk\L10nTranslator\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

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
        $return = false;
        if ($this->getTranslationTarget() === $searchString) {
            $return = true;
        }
        if ($return === false && $search->getIncludeSource() === true) {
            $return = $this->getTranslationSource() === $searchString;
        }
        if ($return === false && $search->getIncludeKey() === true) {
            $return = $this->getTranslationKey() === $searchString;
        }
        return $return;
    }

    /**
     * @param Search $search
     * @return bool
     */
    protected function caseInSensitiveMatchSearch(Search $search)
    {
        $searchString = $search->getSearchString();
        $return = false;
        if (strpos(strtolower($this->getTranslationTarget()), strtolower($searchString)) !== false) {
            $return = true;
        }
        if ($return === false && $search->getIncludeSource() === true) {
            $return = strpos(strtolower($this->getTranslationSource()), strtolower($searchString)) !== false;
        }
        if ($return === false && $search->getIncludeKey() === true) {
            $return = strpos(strtolower($this->getTranslationKey()), strtolower($searchString)) !== false;
        }
        return $return;
    }

    /**
     * @param Search $search
     * @return bool
     */
    protected function caseSensitiveMatchSearch(Search $search)
    {
        $searchString = $search->getSearchString();
        $return = false;
        if (strpos($this->getTranslationTarget(), $searchString) !== false) {
            $return = true;
        }
        if ($return === false && $search->getIncludeSource() === true) {
            $return = strpos($this->getTranslationSource(), $searchString) !== false;
        }
        if ($return === false && $search->getIncludeKey() === true) {
            $return = strpos($this->getTranslationKey(), $searchString) !== false;
        }
        return $return;
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
