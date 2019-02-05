<?php
namespace Lightwerk\L10nTranslator\Domain;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtensionConfiguration
 *
 * Helper Object for convenient access to extension configuration
 */
class ExtensionConfiguration implements SingletonInterface
{

    /**
     * @var array
     */
    protected $configuredFiles = [];

    /**
     * @var array
     */
    protected $absolutePathsToConfiguredFiles = [];

    /**
     * @var bool
     */
    protected $supportsDefault = false;

    public function __construct()
    {
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableL10nFiles'])) {
            $this->configuredFiles = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableL10nFiles'],
                true
            );
        }
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableLanguages'])) {
            $availableLanguages = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableLanguages'],
                true
            );
            $this->supportsDefault = in_array('default', $availableLanguages);
        }
    }

    /**
     * @return array
     */
    public function getConfiguredFiles(): array
    {
        return $this->configuredFiles;
    }

    /**
     * @return array
     */
    public function getAbsolutePathsToConfiguredFiles(): array
    {
        if (!empty($this->configuredFiles) && empty($this->absolutePathsToConfiguredFiles)) {
            foreach ($this->configuredFiles as $configuredFile) {
                $this->absolutePathsToConfiguredFiles[] = GeneralUtility::getFileAbsFileName('EXT:' . $configuredFile);
            }
        }
        return $this->absolutePathsToConfiguredFiles;
    }

    /**
     * @return bool
     */
    public function supportsDefault(): bool
    {
        return $this->supportsDefault;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isFileAvailable(string $file): bool
    {
        return in_array($file, $this->configuredFiles);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isAbsoluteFilePathAvailable(string $file): bool
    {
        return in_array($file, $this->getAbsolutePathsToConfiguredFiles());
    }
}