<?php
namespace Lightwerk\L10nTranslator\Command;

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
use Lightwerk\L10nTranslator\Domain\Model\Search;
use TYPO3\CMS\Extbase\MVC\Controller\CommandController;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class L10nIntegrityCommandController extends CommandController
{
    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     * @inject
     */
    protected $translationFileFactory;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     * @inject
     */
    protected $cacheManager;

    /**
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function listCommand()
    {
        $this->flushCache();
        $search = new Search('', '', '');
        $translationFiles = $this->translationFileFactory->findBySearch($search);
        $countFiles = 0;
        $countDiffFiles = 0;
        foreach ($translationFiles as $translationFile) {
            $countTranslations = count($translationFile->getTranslations());
            $this->outputLine($countTranslations . ' Translations for l10nFile ' . $translationFile->getRelativePath());
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                $countFiles ++;
                $countL10nTranslations = count($l10nTranslationFile->getTranslations());
                $diff = $countTranslations - $countL10nTranslations;
                if ($diff !== 0) {
                    $countDiffFiles ++;
                    $this->outputLine('WARNING: ' . $diff . ' labels missing for ' . $l10nTranslationFile->getLanguage());
                }
            }
        }
        if ($countDiffFiles > 0) {
            $this->outputLine('WARNING: ' . $countDiffFiles . ' of ' . $countFiles . ' differ');
        }
    }

    /**
     * @param string $l10nFile
     * @param string $language
     * @return void
     */
    public function showCommand($l10nFile, $language)
    {
        $this->flushCache();
        $search = new Search('', $language, $l10nFile);
        $translationFiles = $this->translationFileFactory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                foreach ($translationFile->getTranslations() as $translation) {
                    if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                        $this->outputLine('WARNING: ' . $translation->getTranslationKey() . ' - ' . $translation->getTranslationSource());
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function flushCache()
    {
        $cacheFrontend = $this->cacheManager->getCache('l10n');
        $cacheFrontend->flush();
    }
}
