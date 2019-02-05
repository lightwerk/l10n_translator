<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][]
    = \Lightwerk\L10nTranslator\Command\L10nCommandController::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][]
    = \Lightwerk\L10nTranslator\Command\L10nIntegrityCommandController::class;

// XCLASS for enabling reading default language labels from l10n resp. var/labels
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Localization\Parser\XliffParser::class] = [
    'className' => \Lightwerk\L10nTranslator\Localization\Parser\XliffParser::class
];