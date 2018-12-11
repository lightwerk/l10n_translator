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

use TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper;

/**
 * Class StringUtility
 *
 * @author Achim Fritz <af@lightwerk.com>
 * @package TYPO3
 * @subpackage l10n_translator
 */
class IfShouldBeTextAreaViewHelper extends IfViewHelper
{

    const STRLEN_FOR_TEXTAREA = 50;

    /**
     * Renders <f:then> child if $condition is true, otherwise renders <f:else> child.
     *
     * @return string the rendered string
     * @api
     */
    public function render()
    {
        if (static::evaluateCondition($this->arguments)) {
            return $this->renderThenChild();
        } else {
            return $this->renderElseChild();
        }
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
        return isset($arguments['condition']) && (strpos($arguments['condition'], PHP_EOL) !== false || strlen($arguments['condition']) > self::STRLEN_FOR_TEXTAREA);
    }


}
