<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Lightwerk.l10n_translator',
    'web',
    'translator',
    '',
    [
        'TranslationFile' => 'list'
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:l10n_translator/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:l10n_translator/Resources/Private/Language/locallang_translator.xlf',
        'navigationComponentId' => '',
        'inheritNavigationComponentFromMainModule' => false
    ]
);
