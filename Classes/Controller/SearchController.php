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
class SearchController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     * @inject
     */
    protected $l10nConfiguration;

    /**
     * @return void
     */
    protected function initializeAction() {
        parent::initializeAction();
        if ($this->request->hasArgument('search')) {
            $propertyMappingConfiguration = $this->arguments['search']->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowProperties('language');
            $propertyMappingConfiguration->allowProperties('l10nFile');
            $propertyMappingConfiguration->allowProperties('searchString');
        }
    }

    /**
     * @param Search|NULL $search
     * @return void
     */
    public function newAction(Search $search = null)
    {
        $availableL10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        $availableLanguages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $languages = array();
        foreach ($availableLanguages as $availableLanguage) {
            $languages[$availableLanguage] = $availableLanguage;
        }
        $l10nFiles = array();
        foreach ($availableL10nFiles as $availableL10nFile) {
            $l10nFiles[$availableL10nFile] = $availableL10nFile;
        }
        $this->view->assign('l10nFiles', $l10nFiles);
        $this->view->assign('languages', $languages);
        $this->view->assign('search', $search);
    }


}