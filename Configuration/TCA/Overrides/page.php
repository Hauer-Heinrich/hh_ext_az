<?php
defined('TYPO3') || die();

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function(string $extensionKey) {
    // make PageTsConfig selectable
    ExtensionManagementUtility::registerPageTSConfigFile(
        $extensionKey,
        'Configuration/TsConfig/OnlyAzEntries.tsconfig',
        'hh_ext_az - Allow only A-Z Entries'
    );
}, 'hh_ext_az');
