<?php
namespace Lightwerk\L10nTranslator\Configuration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Achim Fritz <af@lightwerk.com>, Lightwerk
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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class L10nConfiguration implements SingletonInterface
{

    /**
     * @return array
     */
    public function getAvailableSystemLanguages()
    {
        $langs = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['lang']['availableLanguages'];
        $availableLanguages = [];
        foreach ($langs as $lang) {
            if (trim($lang) !== '') {
                $availableLanguages[] = $lang;
            }
        }
        return $availableLanguages;
    }

    public function getConfiguration()
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['l10n_translator']);
    }

    /**
     * @return array
     */
    public function getAvailableL10nFiles()
    {
        $configuration = $this->getConfiguration();
        return GeneralUtility::trimExplode(',', $configuration['availableL10nFiles'], true);
    }

    /**
     * @return array
     */
    public function getAvailableL10nLanguages()
    {
        $configuration = $this->getConfiguration();
        return GeneralUtility::trimExplode(',', $configuration['availableLanguages'], true);
    }

    public function isHtmlAllow()
    {
        $configuration = $this->getConfiguration();
        return (bool)$configuration['allowHtmlInLabel'];
    }
}
