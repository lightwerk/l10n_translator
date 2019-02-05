<?php
namespace Lightwerk\L10nTranslator\Domain\Service;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

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
                $xmlFile[] = '				<source>' . htmlspecialchars($translation->getTranslationTarget()) . '</source>';
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
        $xmlFile = [];
        $xmlFile[] = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>';
        $xmlFile[] = '<T3locallangExt>';
        $xmlFile[] = "\t" . '<data type="array">';
        $xmlFile[] = "\t\t" . '<languageKey index="' . $translationFile->getLanguage() . '" type="array">';
        $translations = $translationFile->getTranslations();
        foreach ($translations as $translation) {
            $xmlFile[] = "\t\t\t" . '<label index="' . $translation->getTranslationKey() . '">' . htmlspecialchars($translation->getTranslationTarget()) . '</label>';
        }
        $xmlFile[] = "\t\t" . '</languageKey>';
        $xmlFile[] = "\t" . '</data>';
        $xmlFile[] = '</T3locallangExt>';
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
