<?php

namespace Lightwerk\L10nTranslator\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */


/**
 * Class StringUtility
 *
 * @author Daniel Goerz <dlg@lightwerk.com>
 * @package TYPO3
 * @subpackage l10n_translator
 */
class StringUtility
{

    /**
     * Strips the path to a language file off the string and appends the
     * filename into parentheses.
     *
     * E.g.:
     * news/Resources/Private/Language/locallang.xlf => news (locallang.xlf)
     *
     * @param string $fullPath
     * @return string
     */
    public static function stripPathToLanguageFile($fullPath)
    {
        $pathsToStrip = [
            '/Resources/Private/Language/',
            '/pi1/'
        ];

        foreach ($pathsToStrip as $pathToStrip) {
            if (strpos($fullPath, $pathToStrip) !== false) {
                $stripedString = str_replace($pathToStrip, ' (', $fullPath) . ')';
                return $stripedString;
            }
        }

        return $fullPath;
    }
}