<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use HauerHeinrich\HhExtAz\Controller\EntryController;
use HauerHeinrich\HhExtAz\Controller\MenuController;
use HauerHeinrich\HhExtAz\Controller\SearchController;

call_user_func(function(string $extensionKey) {

    // Plugin 1: Alphabetische Liste inkl. Detailansicht (show)
    ExtensionUtility::configurePlugin(
        $extensionKey,
        'List',
        [
            EntryController::class => 'list, show',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    // Plugin 2: Volltextsuche (uncached, damit GET-Formulare ohne cHash funktionieren)
    ExtensionUtility::configurePlugin(
        $extensionKey,
        'Search',
        [
            SearchController::class => 'search',
        ],
        [
            SearchController::class => 'search',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    // Plugin 3: Sprungmenü (A-Z Buchstabenleiste)
    ExtensionUtility::configurePlugin(
        $extensionKey,
        'Menu',
        [
            MenuController::class => 'menu',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
}, 'hh_ext_az');
