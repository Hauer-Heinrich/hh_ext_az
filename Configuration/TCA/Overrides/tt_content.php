<?php
declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(function(string $extensionKey) {
    $ll = 'LLL:EXT:hh_ext_az/Resources/Private/Language/locallang_db.xlf:';

    $listSignature = ExtensionUtility::registerPlugin(
        $extensionKey,
        'List',
        $ll . 'plugin.list.title',
        'hhextaz-plugin',
        'plugins',
        $ll . 'plugin.list.description'
    );

    $searchSignature = ExtensionUtility::registerPlugin(
        $extensionKey,
        'Search',
        $ll . 'plugin.search.title',
        'hhextaz-plugin',
        'plugins',
        $ll . 'plugin.search.description'
    );

    $menuSignature = ExtensionUtility::registerPlugin(
        $extensionKey,
        'Menu',
        $ll . 'plugin.menu.title',
        'hhextaz-plugin',
        'plugins',
        $ll . 'plugin.menu.description'
    );

    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        // --- List-Plugin ---
        'tx_hhextaz_records' => [
            'exclude' => true,
            'label' => $ll . 'flexform.list.records',
            'description' => $ll . 'flexform.list.records.description',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_hhextaz_domain_model_faq',
                'size' => 8,
                'maxitems' => 999,
                'default' => '',
                'suggestOptions' => [
                    'default' => [
                        'searchWholePhrase' => true,
                    ],
                ],
                'fieldControl' => [
                    'editPopup' => ['disabled' => false],
                    'addRecord' => ['disabled' => false],
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'tx_hhextaz_categories' => [
            'exclude' => true,
            'label' => $ll . 'flexform.list.categories',
            'description' => $ll . 'flexform.list.categories.description',
            'config' => [
                'type' => 'category',
                'relationship' => 'oneToMany',
                'size' => 10,
                'maxitems' => 999,
                'treeConfig' => [
                    'appearance' => [
                        'expandAll' => true,
                        'showHeader' => true,
                    ],
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'tx_hhextaz_category_conjunction' => [
            'exclude' => true,
            'label' => $ll . 'flexform.list.categoryConjunction',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $ll . 'flexform.list.categoryConjunction.or', 'value' => 'or'],
                    ['label' => $ll . 'flexform.list.categoryConjunction.and', 'value' => 'and'],
                ],
                'default' => 'or',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'tx_hhextaz_sort_field' => [
            'exclude' => true,
            'label' => $ll . 'flexform.list.sortField',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $ll . 'flexform.list.sortField.sorting', 'value' => 'sorting'],
                    ['label' => $ll . 'flexform.list.sortField.question', 'value' => 'question'],
                    ['label' => $ll . 'flexform.list.sortField.crdate', 'value' => 'crdate'],
                ],
                'default' => 'sorting',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'tx_hhextaz_sort_order' => [
            'exclude' => true,
            'label' => $ll . 'flexform.list.sortOrder',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $ll . 'flexform.list.sortOrder.asc', 'value' => 'asc'],
                    ['label' => $ll . 'flexform.list.sortOrder.desc', 'value' => 'desc'],
                ],
                'default' => 'asc',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
    ]);

    $GLOBALS['TCA']['tt_content']['palettes']['hhextaz_sorting'] = [
        'showitem' => 'tx_hhextaz_sort_field, tx_hhextaz_sort_order',
    ];

    $GLOBALS['TCA']['tt_content']['types'][$listSignature] = [
        'showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
                bodytext,
            --div--;' . $ll . 'plugin.tab,
                tx_hhextaz_records,
                tx_hhextaz_categories,
                tx_hhextaz_category_conjunction,
                pages;' . $ll . 'plugin.list.pages,
                recursive,
            --div--;' . $ll . 'flexform.list.sheet.display,
                --palette--;;hhextaz_sorting,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;;frames,
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
        ',
        'columnsOverrides' => [
            'bodytext' => [
                'config' => [
                    'enableRichtext' => true,
                ],
            ],
        ],
    ];
}, 'hh_ext_az');
