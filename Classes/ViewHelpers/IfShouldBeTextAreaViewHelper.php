<?php

namespace Lightwerk\L10nTranslator\ViewHelpers;

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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Class StringUtility
 *
 * @author Achim Fritz <af@lightwerk.com>
 * @package TYPO3
 * @subpackage l10n_translator
 */
class IfShouldBeTextAreaViewHelper extends AbstractConditionViewHelper
{

    const STRLEN_FOR_TEXTAREA = 50;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('input', 'string', 'String to be evaluated.');
    }


    /**
     * Returns true if the $arguments['input'] string either
     *   * contains a line break
     *   * exceeds 50 characters
     *
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return isset($arguments['input']) && (strpos($arguments['input'], PHP_EOL) !== false || strlen($arguments['input']) > self::STRLEN_FOR_TEXTAREA);
    }


}
