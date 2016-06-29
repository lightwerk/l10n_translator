<?php
namespace Lightwerk\L10nTranslator\Controller;

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

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     * @inject
     */
    protected $translationFileFactory;

    /**
     * @param Search $search
     * @return void
     */
    public function listAction(Search $search)
    {
        try {
            $translationFiles = $this->translationFileFactory->findBySearch($search);
        } catch (\Lightwerk\L10nTranslator\Exception $e) {
            $this->addErrorFlashMessage($e->getMessage() . ' - ' . $e->getCode());
        }
        $this->view->assign('search', $search);
        $this->view->assign('translationFiles', $translationFiles);
    }


}