<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function(string $extensionKey) {
    ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'Hauer-Heinrich - A-Z'
    );
}, 'hh_ext_az');
