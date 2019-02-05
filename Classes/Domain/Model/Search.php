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
class Search
{
    /**
     * @var string
     */
    protected $searchString = '';
    
    /**
     * @var string
     */
    protected $language = '';
    
    /**
     * @var string
     */
    protected $l10nFile = '';

    /**
     * @var bool
     */
    protected $caseSensitive = false;

    /**
     * @var bool
     */
    protected $exactMatch = false;

    /**
     * @var bool
     */
    protected $includeSource = true;

    /**
     * @var bool
     */
    protected $includeKey = true;

    /**
     * For unmark the flag in the exactSearch, if the search come from the link of the defaultSource
     * @var bool
     */
    protected $onlyOneTimeExactSearch = false;

    /**
     * @param string $searchString
     * @param string $language
     * @param string $l10nFile
     * @param bool $caseSensitive
     * @param bool $exactMatch
     * @param bool $includeSource
     * @param bool $includeKey
     * @param bool $onlyOneTimeExactSearch
     */
    public function __construct($searchString = '', $language = '', $l10nFile = '', $caseSensitive = false, $exactMatch = false, $includeSource = true, $includeKey = true, $onlyOneTimeExactSearch = false)
    {
        $this->searchString = $searchString;
        $this->language = $language;
        $this->l10nFile = $l10nFile;
        $this->caseSensitive = $caseSensitive;
        $this->exactMatch = $exactMatch;
        $this->includeSource = $includeSource;
        $this->includeKey = $includeKey;
        $this->onlyOneTimeExactSearch = $onlyOneTimeExactSearch;
    }

    /**
     * @return string $searchString
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    
    /**
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    
    /**
     * @return string $l10nFile
     */
    public function getL10nFile()
    {
        return $this->l10nFile;
    }

    /**
     * @return boolean
     */
    public function getCaseSensitive()
    {
        return $this->caseSensitive;
    }

    /**
     * @return boolean
     */
    public function getExactMatch()
    {
        return $this->exactMatch;
    }

    /**
     * @return boolean
     */
    public function getIncludeSource()
    {
        return $this->includeSource;
    }

    /**
     * @return bool
     */
    public function hasLanguage()
    {
        return $this->language !== '';
    }

    /**
     * @return bool
     */
    public function hasL10nFile()
    {
        return $this->l10nFile !== '';
    }

    /**
     * @return bool
     */
    public function hasSearchString()
    {
        return $this->searchString !== '';
    }

    /**
     * @return bool
     */
    public function getIncludeKey()
    {
        return $this->includeKey;
    }

    /**
     * for searching from table row and don't set the checkbox for exact match
     * @return bool
     */
    public function checkIfIgnoreExactMatchInView()
    {
        if ($this->onlyOneTimeExactSearch) {
            $this->exactMatch = false;
            return true;
        }
        return false;
    }

}
