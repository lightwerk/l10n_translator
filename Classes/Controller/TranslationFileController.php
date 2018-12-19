<?php
namespace Lightwerk\L10nTranslator\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Achim Fritz <af@lightwerk.com>, Lightwerk GmbH
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
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     */
    protected $translationFileFactory;

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     */
    protected $l10nConfiguration;

    /**
     * @var \Lightwerk\L10nTranslator\Utility\StringUtility
     */
    protected $stringUtility;

    /**
     * @param \Lightwerk\L10nTranslator\Configuration\L10nConfiguration $l10nConfiguration
     * @return void
     */
    public function injectL10nConfiguration(\Lightwerk\L10nTranslator\Configuration\L10nConfiguration $l10nConfiguration)
    {
        $this->l10nConfiguration = $l10nConfiguration;
    }

    /**
     * @param \Lightwerk\L10nTranslator\Utility\StringUtility $stringUtility
     * @return void
     */
    public function injectStringUtility(\Lightwerk\L10nTranslator\Utility\StringUtility $stringUtility)
    {
        $this->stringUtility = $stringUtility;
    }

    /**
     * @param \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory $translationFileFactory
     * @return void
     */
    public function injectTranslationFileFactory(\Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory $translationFileFactory)
    {
        $this->translationFileFactory = $translationFileFactory;
    }

    /**
     * @return void
     */
    protected function initializeListAction()
    {
        parent::initializeAction();
        if ($this->request->hasArgument('search')) {
            /** @var PropertyMappingConfiguration $propertyMappingConfiguration */
            $propertyMappingConfiguration = $this->arguments['search']->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowProperties('language');
            $propertyMappingConfiguration->allowProperties('l10nFile');
            $propertyMappingConfiguration->allowProperties('searchString');
            $propertyMappingConfiguration->allowProperties('exactMatch');
            $propertyMappingConfiguration->allowProperties('caseInSensitive');
            $propertyMappingConfiguration->allowProperties('includeSource');
            $propertyMappingConfiguration->allowProperties('includeKey');
            // for searching from table row and don't set the checkbox for exact match
            $propertyMappingConfiguration->allowProperties('onlyOneTimeExactSearch');
        }
    }

    /**
     * @param \Lightwerk\L10nTranslator\Domain\Model\Search $search
     * @return void
     */
    public function listAction(Search $search = null)
    {
        $this->view->getModuleTemplate()->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/L10nTranslator/L10nTranslator');
        $translationFiles = [];
        $availableL10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        $availableLanguages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $languages = [];
        foreach ($availableLanguages as $availableLanguage) {
            $languages[$availableLanguage] = $availableLanguage;
        }
        $l10nFiles = [];
        foreach ($availableL10nFiles as $availableL10nFile) {
            $l10nFiles[$availableL10nFile] = $this->stringUtility->stripPathToLanguageFile($availableL10nFile);
        }
        $this->view->assign('l10nFiles', $l10nFiles);
        $this->view->assign('languages', $languages);
        if ($search !== null) {
            try {
                $translationFiles = $this->translationFileFactory->findBySearch($search);
            } catch (\Lightwerk\L10nTranslator\Exception $e) {
                $this->addFlashMessage($e->getMessage() . ' - ' . $e->getCode(), '', FlashMessage::ERROR);
            }
        }
        if ($search !== null) {
            if ($search->checkIfIgnoreExactMatchInView()) {
                $this->addFlashMessage('', 'Search with exact match', FlashMessage::INFO);
            };
        }
        $this->view->assign('search', $search);
        $this->view->assign('translationFiles', $translationFiles);
    }
}
