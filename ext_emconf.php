<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'l10n Translator',
    'description' => 'translate files in l10n folder',
    'category' => 'module',
    'author' => 'Achim Fritz, Lightwerk GmbH',
    'author_email' => 'af@lightwerk.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
