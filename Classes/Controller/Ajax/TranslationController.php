<?php
namespace Lightwerk\L10nTranslator\Controller\Ajax;

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


use Lightwerk\L10nTranslator\Configuration\L10nConfiguration;
use Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory;
use Lightwerk\L10nTranslator\Domain\Model\Translation;
use Lightwerk\L10nTranslator\Domain\Service\TranslationFileService;
use Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\JsonResponse;
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
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     */
    protected $translationFileFactory;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     */
    protected $l10nConfiguration;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService
     */
    protected $translationFileWriterService;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileService
     */
    protected $translationFileService;

    /**
     * @param ServerRequestInterface $request
     * @return JsonResponse
     */
    public function update(ServerRequestInterface $request): JsonResponse
    {
        $this->initializeObjects();
        try {
            $this->assureModuleAccess();
            $postParams = $request->getParsedBody();
            $this->validateRequest($postParams);
            $translationFile = $this->translationFileFactory->findByPath($postParams['path']);
            $l10nTranslationFile = $translationFile->getL10nTranslationFile($postParams['language']);
            $translation = new Translation($postParams['path'], $postParams['key'], $postParams['target']);
            $l10nTranslationFile->upsertTranslationTarget($translation);
            $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
            if ($postParams['language'] === 'default') {
                $this->translationFileService->updateSourceInFiles($postParams);
            }
            $this->flushCache();
            $content = [
                    'flashMessage' => [
                    'title' => 'OK',
                    'message' => 'label updated',
                    'severity' => FlashMessage::OK
                ]
            ];
        } catch (\Exception $e) {
            $content = [
                'flashMessage' => [
                    'title' => 'ERROR',
                    'message' => $e->getMessage() . ' - ' . $e->getCode(),
                    'severity' => FlashMessage::ERROR
                ]
            ];
        }

        return new JsonResponse($content);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function assureModuleAccess()
    {
        $beUser = $this->getBeUser();
        if ($beUser->check('modules', 'web_L10nTranslatorTranslator') === false) {
            throw new Exception('Access Denied', 1469781234);
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBeUser()
    {
        return $GLOBALS['BE_USER'];
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
        $this->translationFileService = $this->objectManager->get(TranslationFileService::class);
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
     * @param mixed $postParams
     * @throws Exception
     */
    protected function validateRequest($postParams)
    {
        if (!is_array($postParams)) {
            throw new Exception('Invalid request.', 1467175555);
        }

        if (isset($postParams['language']) === false || isset($postParams['target']) === false || isset($postParams['key']) === false || isset($postParams['path']) === false) {
            throw new Exception('Invalid request.', 1467175555);
        }
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        if (in_array($postParams['language'], $languages) === false) {
            throw new Exception('Language not configured: ' . $postParams['language'], 1467175550);
        }
        if (in_array($postParams['path'], $l10nFiles) === false) {
            throw new Exception('Path not configured: ' . $postParams['path'], 1467175551);
        }
        if (empty($postParams['key']) === true) {
            throw new Exception('Key must not be empty.', 1467175554);
        }
        if ($postParams['target'] !== strip_tags($postParams['target']) && !$this->l10nConfiguration->isHtmlAllow()) {
            throw new Exception('HTML not allowed.', 1467175552);
        }
    }
}
