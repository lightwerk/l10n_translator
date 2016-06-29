<?php
namespace Lightwerk\L10nTranslator\Controller\Ajax;

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


use Lightwerk\L10nTranslator\Configuration\L10nConfiguration;
use Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory;
use Lightwerk\L10nTranslator\Domain\Model\Translation;
use Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationController
{

    /**
     * @var TranslationFileFactory
     */
    protected $translationFileFactory;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var L10nConfiguration
     */
    protected $l10nConfiguration;

    /**
     * @var TranslationFileWriterService
     */
    protected $translationFileWriterService;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @param array $params Array of parameters from the AJAX interface, currently unused
     * @param AjaxRequestHandler $ajaxObj Object of type AjaxRequestHandler
     * @return void
     */
    public function update($params = [], AjaxRequestHandler &$ajaxObj = null)
    {

        $this->initializeObjects();
        try {
            $request = $this->getRequest();
            $translationFile = $this->translationFileFactory->findByPath($request['path']);
            $l10nTranslationFile = $translationFile->getL10nTranslationFile($request['language']);
            $translation = new Translation($request['path'], $request['key'], $request['target']);
            $l10nTranslationFile->replaceTranslationTarget($translation);
            $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
            $this->flushCache();
            $flashMessage = array(
                'title' => 'OK',
                'message' => 'label updated',
                'severity' => FlashMessage::OK
            );
        } catch (\Exception $e) {
            $flashMessage = array(
                'title' => 'ERROR',
                'message' => $e->getMessage() . ' - ' . $e->getCode(),
                'severity' => FlashMessage::ERROR
            );
        }

        $ajaxObj->setContentFormat('json');
        $ajaxObj->addContent('flashMessage', $flashMessage);

    }

    /**
     * @return void
     */
    protected function initializeObjects()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->l10nConfiguration = $this->objectManager->get(L10nConfiguration::class);
        $this->translationFileFactory = $this->objectManager->get(TranslationFileFactory::class);
        $this->translationFileWriterService = $this->objectManager->get(TranslationFileWriterService::class);
        $this->cacheManager = $this->objectManager->get(CacheManager::class);
    }

    /**
     * @return void
     */
    protected function flushCache()
    {
        $cacheFrontend = $this->cacheManager->getCache('l10n');
        $cacheFrontend->flush();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getRequest()
    {
        $request = GeneralUtility::_POST();
        if (isset($request['language']) === false || isset($request['target']) === false || isset($request['key']) === false || isset($request['path']) === false) {
            throw new Exception('Invalid request.', 1467175555);
        }
        $language = $request['language'];
        $path = $request['path'];
        $target = $request['target'];
        $key = $request['key'];
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        if (in_array($language, $languages) === false) {
            throw new Exception('Language not configured: ' . $language, 1467175550);
        }
        if (in_array($path, $l10nFiles) === false) {
            throw new Exception('Path not configured: ' . $path, 1467175551);
        }
        if ($target !== htmlspecialchars($target)) {
            throw new Exception('HTML not allowed.', 1467175552);
        }
        if (empty($key) === true) {
            throw new Exception('Source may not be empty.', 1467175554);
        }
        return $request;
    }
}
