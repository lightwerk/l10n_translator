<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][]
    = \Lightwerk\L10nTranslator\Command\L10nCommandController::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][]
    = \Lightwerk\L10nTranslator\Command\L10nIntegrityCommandController::class;
