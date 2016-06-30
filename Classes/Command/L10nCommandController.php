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
class L10nCommandController extends CommandController
{

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     * @inject
     */
    protected $translationFileFactory;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService
     * @inject
     */
    protected $translationFileWriterService;

    /**
     * @var \Lightwerk\L10nTranslator\Domain\Service\TranslationFileService
     * @inject
     */
    protected $translationFileService;

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     * @inject
     */
    protected $l10nConfiguration;
    

    /**
     * @param string $xlfFile
     * @param boolean $createEmptyLabels
     * @return void
     */
    public function allXml2XlfByDefaultXlfCommand($xlfFile, $createEmptyLabels = false)
    {
        $this->translationFileService->allXml2XlfByDefaultXlf($xlfFile, $createEmptyLabels);
    }

    /**
     * @param string $xlfFile
     * @param string $language
     * @param boolean $createEmptyLabels
     * @return void
     */
    public function xml2XlfByDefaultXlfCommand($xlfFile, $language, $createEmptyLabels = false)
    {
        $this->translationFileService->xml2XlfByDefaultXlf($xlfFile, $language, $createEmptyLabels);
    }

    /**
     * @param string $language
     * @param bool $copyLabels
     * @return void
     */
    public function createMissingFilesCommand($language, $copyLabels = true)
    {
        $this->translationFileService->createMissingFiles($language, $copyLabels);
    }

    /**
     * @param string $xlfFile
     * @param string $language
     * @param string $altLanguage
     * @return void
     */
    public function overwriteWithAltLanguageCommand($xlfFile, $language, $altLanguage)
    {
        $this->translationFileService->overwriteWithAltLanguage($xlfFile, $language, $altLanguage);
    }


    /**
     * @param string $searchString
     * @param string $language
     * @param string $extension
     * @return void
     */
    public function listCommand($searchString = '', $language = '', $extension = '')
    {
        $search = new Search($searchString, $language, $extension);
        $translationFiles = $this->translationFileFactory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                $translations = $l10nTranslationFile->getMatchedTranslations();
                foreach ($translations as $translation) {
                    $this->outputLine($l10nTranslationFile->getLanguage() . ' ' . $translation->getTranslationKey() . ': ' . $translation->getTranslationTarget());
                }
            }
        }
    }
}
