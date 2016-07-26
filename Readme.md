L10n Translator
=====

Translate Files in the l10n Folder, keep your l10n Folder clean

Configuration
----

insert your l10nFiles and languages in the Extension Configuration

Features
----

* convert xml to xlf (CLI)
* create missing l10n files (CLI)
* create missing l10n translation (CLI)
* proof integrity (CLI)
* edit existing labels (BE)

TODOs
----

* handle also global Extensions (typo3conf/ext "hardcoded")
* integrity and actions in BE

CLI Examples
----

* `typo3/cli_dispatch.phpsh extbase l10n:allxml2XlfByDefaultXlf --xlfFile=powermail/Resources/Private/Language/locallang.xlf`
* `typo3/cli_dispatch.phpsh extbase l10n:xml2XlfByDefaultXlf --xlfFile=solr/Resources/Private/Language/PluginSearch/locallang.xlf --language=de`
* `typo3/cli_dispatch.phpsh extbase l10n:createMissingFiles --language=de`
* `typo3/cli_dispatch.phpsh extbase l10n:overwriteWithLanguage --l10nFile=powermail/Resources/Private/Language/locallang.xlf --language=es --sourceLanguage=de`
* `typo3/cli_dispatch.phpsh extbase l10n:createMissingLabels --l10nFile=powermail/Resources/Private/Language/locallang.xlf --language=es --sourceLanguage=de`