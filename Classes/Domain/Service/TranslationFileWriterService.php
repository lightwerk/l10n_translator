<?php
namespace Lightwerk\L10nTranslator\Domain\Service;

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

use Lightwerk\L10nTranslator\Domain\Model\AbstractTranslationFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileWriterService implements SingletonInterface
{

    /**
     * @param AbstractTranslationFile $translationFile
     * @return void
     * @throws Exception
     */
    public function writeTranslation(AbstractTranslationFile $translationFile)
    {
        if ($translationFile->getSplFileInfo()->getExtension() === 'xlf') {
            $this->writeTranslationXlf($translationFile);
        } elseif ($translationFile->getSplFileInfo()->getExtension() === 'xml') {
            $this->writeTranslationXml($translationFile);
        } else {
            throw new Exception('unknown Extension ' . $translationFile->getSplFileInfo()->getExtension(), 1467184635);
        }
    }

    /**
     * @param AbstractTranslationFile $translationFile
     * @return void
     * @throws Exception
     */
    public function writeTranslationXlf(AbstractTranslationFile $translationFile)
    {
        $xmlFile = [];
        $language = $translationFile->getLanguage();
        $extension = $translationFile->getExtension();

        $xmlFile[] = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>';
        $xmlFile[] = '<xliff version="1.0">';
        $xmlFile[] = '	<file source-language="en"' . ($language !== 'default' ? ' target-language="' . $language . '"' : '')
            . ' datatype="plaintext" original="messages" date="' . gmdate('Y-m-d\TH:i:s\Z') . '"'
            . ' product-name="' . $extension . '">';
        $xmlFile[] = '		<header/>';
        $xmlFile[] = '		<body>';
        $translations = $translationFile->getTranslations();
        foreach ($translations as $translation) {
            if ($language === 'default') {
                $xmlFile[] = '			<trans-unit id="' . $translation->getTranslationKey() . '">';
                $xmlFile[] = '				<source>' . htmlspecialchars($translation->getTranslationSource()) . '</source>';
                $xmlFile[] = '			</trans-unit>';
            } else {
                $xmlFile[] = '			<trans-unit id="' . $translation->getTranslationKey() . '">';
                $xmlFile[] = '				<source>' . htmlspecialchars($translation->getTranslationSource()) . '</source>';
                $xmlFile[] = '				<target>' . htmlspecialchars($translation->getTranslationTarget()) . '</target>';
                $xmlFile[] = '			</trans-unit>';
            }
        }

        $xmlFile[] = '		</body>';
        $xmlFile[] = '	</file>';
        $xmlFile[] = '</xliff>';

        if (is_dir($translationFile->getSplFileInfo()->getPath()) === false) {
            try {
                GeneralUtility::mkdir_deep($translationFile->getSplFileInfo()->getPath());
            } catch (\Exception $e) {
                throw new Exception('Cannot create directory file ' . $translationFile->getSplFileInfo()->getPath() . '. Error: ' . $e->getMessage(), 1466440410);
            }
        }
        $xml = implode(LF, $xmlFile);
        $this->assureValidXml($xml, $translationFile);
        $res = GeneralUtility::writeFile(str_replace('.xml', '.xlf', $translationFile->getCleanPath()), $xml);
        if ($res === false) {
            throw new Exception('cannot write file ' . $translationFile->getCleanPath(), 1466440408);
        }
    }

    /**
     * @param AbstractTranslationFile $translationFile
     * @return void
     * @throws Exception
     */
    public function writeTranslationXml(AbstractTranslationFile $translationFile)
    {
        $xmlOptions = array(
            'parentTagMap'=>array(
                'data'=>'languageKey',
                'orig_hash'=>'languageKey',
                'orig_text'=>'languageKey',
                'labelContext'=>'label',
                'languageKey'=>'label'
            )
        );

        $xmlFile = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.chr(10);
        $xmlFile .= GeneralUtility::array2xml($translationFile->translationsToArray(), '', 0, 'T3locallangExt', 0, $xmlOptions);
        $xml = implode(LF, $xmlFile);
        $this->assureValidXml($xml, $translationFile);
        $res = GeneralUtility::writeFile($translationFile->getCleanPath(), $xml);
        if ($res === false) {
            throw new Exception('cannot write file ' . $translationFile->getCleanPath(), 1466440409);
        }
    }

    /**
     * @param string $xml
     * @param AbstractTranslationFile $translationFile
     * @return void
     * @throws \Lightwerk\L10nTranslator\Domain\Service\Exception
     */
    protected function assureValidXml($xml, AbstractTranslationFile $translationFile)
    {
        try {
            $xmlObject = new \SimpleXMLElement($xml);
        } catch (\Exception $e) {
            throw new Exception('invalide XML ' . $translationFile->getCleanPath(), 1468492172);
        }
    }
}
