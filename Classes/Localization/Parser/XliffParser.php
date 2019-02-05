<?php
namespace Lightwerk\L10nTranslator\Localization\Parser;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Lightwerk\L10nTranslator\Domain\ExtensionConfiguration;
use TYPO3\CMS\Core\Localization\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class XliffParser
 *
 * XCLASS to support default.locallang.xlf files that are written to by
 * this extension. By default TYPO3 does not respect localizedFileNames
 * for language `default`.
 */
class XliffParser extends \TYPO3\CMS\Core\Localization\Parser\XliffParser
{

    /**
     * Returns parsed representation of XML file.
     *
     * @param string $sourcePath Source file path
     * @param string $languageKey Language key
     * @return array
     * @throws \TYPO3\CMS\Core\Localization\Exception\FileNotFoundException
     */
    public function getParsedData($sourcePath, $languageKey)
    {
        if ($languageKey !== 'default') {
            return parent::getParsedData($sourcePath, $languageKey);
        }

        $l10nManagerConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        if ($l10nManagerConfiguration->supportsDefault() === false) {
            return parent::getParsedData($sourcePath, $languageKey);
        }

        if ($l10nManagerConfiguration->isAbsoluteFilePathAvailable($sourcePath) === false) {
            return parent::getParsedData($sourcePath, $languageKey);
        }

        // copied from parent::getParsedData from here on
        $this->sourcePath = $sourcePath;
        $this->languageKey = $languageKey;
        $this->sourcePath = $this->getLocalizedFileName($this->sourcePath, $this->languageKey);
        if (!@is_file($this->sourcePath)) {
            // Global localization is not available, try split localization file
            $this->sourcePath = $this->getLocalizedFileName($sourcePath, $languageKey, true);
        }
        if (!@is_file($this->sourcePath)) {
            throw new FileNotFoundException('Localization file does not exist', 1306332397);
        }
        $LOCAL_LANG = [];
        $LOCAL_LANG[$languageKey] = $this->parseXmlFile();
        return $LOCAL_LANG;
    }
}