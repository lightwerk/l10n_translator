<?php
namespace Lightwerk\L10nTranslator\Domain\Validator;


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
use Lightwerk\L10nTranslator\Domain\Model\Search;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class SearchValidator extends AbstractValidator
{

    /**
     * isValid
     *
     * @param Search $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (strlen($value->getSearchString()) < 3 && $value->hasL10nFile() === false && $value->hasLanguage() === false) {
            $this->addError('Empty Search is not allowed', 1466595470);
        }
    }
}
