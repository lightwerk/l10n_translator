<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// XCLASS for enabling reading default language labels from l10n resp. var/labels
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Localization\Parser\XliffParser::class] = [
    'className' => \Lightwerk\L10nTranslator\Localization\Parser\XliffParser::class
];
