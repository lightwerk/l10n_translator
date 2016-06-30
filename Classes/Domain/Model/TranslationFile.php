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
class TranslationFile extends AbstractTranslationFile
{

    const FOLDER = 'typo3conf/ext';

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

        $pathPart = str_replace('/', '\/', PATH_site . self::FOLDER . DIRECTORY_SEPARATOR);
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
            $this->l10nTranslationFiles[$language] = $l10nTranslationFile;
        }
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
        $path = PATH_site . L10nTranslationFile::FOLDER . DIRECTORY_SEPARATOR . $language;
        $path .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $language . '.' . $this->getSplFileInfo()->getBasename();
        return $path;
    }

    /**
     * @param Search $search
     * @return void
     */
    public function applySearch(Search $search)
    {
        if ($search->getSearchString() !== '') {
            foreach ($this->getL10nTranslationFiles() as $l10nTranslationFile) {
                $l10nTranslationFile->applySearch($search);
            }
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
