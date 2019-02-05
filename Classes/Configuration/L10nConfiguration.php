<?php
namespace Lightwerk\L10nTranslator\Configuration;

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
 * @package TYPO3
 * @subpackage l10n_translator
 *
 * Helper Class for more convenient access to the extension configuration
 */
class L10nConfiguration implements SingletonInterface
{

    /**
     * @var array
     */
    protected $availableL10nFiles = [];

    /**
     * @var array
     */
    protected $absolutePathsToConfiguredFiles = [];

    /**
     * @var bool
     */
    protected $supportsDefault = false;

    /**
     * @var bool
     */
    protected $allowHtmlInLabel = false;

    /**
     * @var array
     */
    protected $availableLanguages = [];

    /**
     * L10nConfiguration constructor.
     */
    public function __construct()
    {
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableL10nFiles'])) {
            $this->availableL10nFiles = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableL10nFiles'],
                true
            );
        }

        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableLanguages'])) {
            $this->availableLanguages = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableLanguages'],
                true
            );
            $this->supportsDefault = in_array('default', $this->availableLanguages);
        }

        $this->allowHtmlInLabel = (bool)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['allowHtmlInLabel'];
    }

    /**
     * @return array
     */
    public function getAvailableL10nFiles(): array
    {
        return $this->availableL10nFiles;
    }

    /**
     * @return array
     */
    public function getAbsolutePathsToConfiguredFiles(): array
    {
        if (!empty($this->availableL10nFiles) && empty($this->absolutePathsToConfiguredFiles)) {
            foreach ($this->availableL10nFiles as $availableL10nFile) {
                $this->absolutePathsToConfiguredFiles[] = GeneralUtility::getFileAbsFileName('EXT:' . $availableL10nFile);
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
        return in_array($file, $this->availableL10nFiles);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isAbsoluteFilePathAvailable(string $file): bool
    {
        return in_array($file, $this->getAbsolutePathsToConfiguredFiles());
    }

    /**
     * @return array
     */
    public function getAvailableL10nLanguages(): array
    {
        return $this->availableLanguages;
    }

    /**
     * @return bool
     */
    public function isHtmlAllowed(): bool
    {
        return $this->allowHtmlInLabel;
    }
}
