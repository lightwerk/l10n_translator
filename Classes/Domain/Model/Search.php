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
    protected $includeSource = false;

    /**
     * @param string $searchString
     * @param string $language
     * @param string $l10nFile
     * @param bool $caseSensitive
     * @param bool $exactMatch
     * @param bool $includeSource
     */
    public function __construct($searchString = '', $language = '', $l10nFile = '', $caseSensitive = false, $exactMatch = false, $includeSource = false)
    {
        $this->searchString = $searchString;
        $this->language = $language;
        $this->l10nFile = $l10nFile;
        $this->caseSensitive = $caseSensitive;
        $this->exactMatch = $exactMatch;
        $this->includeSource = $includeSource;
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
}
