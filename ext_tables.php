<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Lightwerk.' . $_EXTKEY,
        'web',
        'translator',
        '',
        array(
            'TranslationFile' => 'list'
        ),
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_translator.xlf',
        )
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
        'L10nTranslator::translation::update',
        'Lightwerk\\L10nTranslator\\Controller\\Ajax\\TranslationController->update'
    );
}
