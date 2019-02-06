<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'l10n Translator',
    'description' => 'translate files in l10n folder',
    'category' => 'module',
    'author' => 'Achim Fritz, Daniel Goerz, Michael Giek',
    'author_email' => 'af@lightwerk.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '>=9.5.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
