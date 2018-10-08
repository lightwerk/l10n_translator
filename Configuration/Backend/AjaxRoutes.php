<?php

/**
 * Definitions for routes provided by EXT:l10n_translator
 */
return [
    'L10nTranslator_update' => [
        'path' => '/L10nTranslator/translation/update',
        'target' => Lightwerk\L10nTranslator\Controller\Ajax\TranslationController::class . '::update'
    ]
];
