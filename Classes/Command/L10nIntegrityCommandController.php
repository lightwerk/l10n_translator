<?php
namespace Lightwerk\L10nTranslator\Command;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use Lightwerk\L10nTranslator\Domain\Model\Search;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class L10nIntegrityCommandController extends Command
{
    /**
     * @var \Lightwerk\L10nTranslator\Domain\Factory\TranslationFileFactory
     */
    protected $translationFileFactory;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;


    /**
     * @param \TYPO3\CMS\Core\Cache\CacheManager $cacheManager
     * @return void
     */
    public function injectCacheManager(\TYPO3\CMS\Core\Cache\CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
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
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('l10n:integrity')
            ->setDescription('not migrated to commands');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('not mgirated to symfony commands');
        return 0;
    }

    /**
     * @return void
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
