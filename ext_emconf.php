<?php

$EM_CONF[$_EXTKEY] = array(
    'title'            => 'l10n Translator',
    'description'      => 'translate files in l10n folder',
    'category'         => 'module',
    'author'           => 'Achim Fritz, Lightwerk GmbH',
    'author_email'     => 'af@lightwerk.com',
    'state'            => 'stable',
    'internal'         => '',
    'uploadfolder'     => '0',
    'createDirs'       => '',
    'clearCacheOnLoad' => 0,
    'version'          => '1.1.1',
    'constraints'      => array(
        'depends'   => array(
            'typo3' => '7.6.0-7.6.99',
        ),
        'conflicts' => array(),
        'suggests'  => array(),
    ),
);
