L10n Translator
=====

Extension for managing the files located in the l10n folder of a TYPO3 installation. It provides several CLI commands for
file and label handling as well as a backend module to translate any label.

Configuration
----

Add all paths to XLF files you want to handle with this extension (e.g. news/Resources/Private/Languages/locallang.xlf) 
alongside all languages you want to support in the extension manager configuration.

Features
----

* Convert XML to XLF (CLI)
* Create missing files in the l10n folder (CLI)
* Create missing labels in files in the l10n folder (CLI)
* Proof integrity of language file sin the l10n folder (CLI)
* Edit existing labels in a custom backend module (BE)

CLI Examples
----

Execute all CLI commands 

* in TYPO3 7LTS via the prefix `typo3/cli_dispatch.phpsh extbase`
* in TYPO3 8LTS via the prefix `typo3/sysext/core/bin/typo3`
* With typo3-console via the prefix `./vendor/bin/typo3cms`

Create all missing files in l10n/de. This will create copies of all configured files that are not there yet.
`l10n:createMissingFiles --language=de`

Create all missing labels in l10n/es in all configured files.
`l10n:createMissingLabels --l10nFile=powermail/Resources/Private/Language/locallang.xlf --language=es`

Create all missing labels for powermail in spanish and fills the source language with german labels.
`l10n:createMissingLabels --l10nFile=powermail/Resources/Private/Language/locallang.xlf --language=es --sourceLanguage=de`

Override all labels in a specific file with labels from another language.
`l10n:overwriteWithLanguage --l10nFile=powermail/Resources/Private/Language/locallang.xlf --language=es --sourceLanguage=de`

Create all missing files for all configured languages
`typo3cms l10n:createmissingfilesforallconfiguredlanguages`

Create all missing labels for all configured languages
`l10n:createallmissinglabelsforallconfiguredlanguages`

Create all missing files for all existing sys_languages
`typo3cms l10n:createmissingfilesforallsystemlanguages`

Create all missing labels for all existing sys_languages
`l10n:createallmissinglabelsforallsystemlanguages`


Convert a XML File with multilanguages to XLF Files
----

the first command copy the locallang.xml file to all languages in the l10n-Folder, for preparing the second command, which writes the XLF-Files

* `typo3/cli_dispatch.phpsh extbase l10n:prepareXmlLanguageFiles --xmlFile=<extKey>/Resources/Private/Language/locallang.xml`
* `typo3/cli_dispatch.phpsh extbase l10n:allxml2xlf --xmlFile=<extKey>/Resources/Private/Language/locallang.xml`


TODOs
----
* Also handle system extensions (currently "typo3conf/ext" is hardcoded)
* Integrity and actions in BE