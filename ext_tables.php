<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Lightwerk.' . $_EXTKEY,
    'web',
    'translator',
    '',
    [
        'TranslationFile' => 'list'
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.svg',
        'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_translator.xlf',
        'navigationComponentId' => '',
        'inheritNavigationComponentFromMainModule' => false
    ]
);
