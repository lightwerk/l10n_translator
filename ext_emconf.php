<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'l10n Translator',
	'description' => 'translate files in l10n folder',
	'category' => 'plugin',
	'author' => 'Achim Fritz',
	'author_email' => 'af@achimfritz.de',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '7.6.0-7.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);