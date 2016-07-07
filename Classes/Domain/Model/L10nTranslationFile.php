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
class L10nTranslationFile extends AbstractTranslationFile
{

    const FOLDER = 'typo3conf/l10n';

    /**
     * @var TranslationFile
     */
    protected $translationFile;

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
        $pathPart = str_replace('/', '\/', PATH_site. self::FOLDER . DIRECTORY_SEPARATOR);
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
     * @param Search $search
     * @return void
     */
    public function applySearch(Search $search)
    {
        if ($search->hasSearchString()) {
            $this->matchedTranslations = $this->getTranslationsBySearch($search);
        }
    }
    
    /**
     * @return TranslationFile
     */
    public function getTranslationFile()
    {
        return $this->translationFile;
    }
}
