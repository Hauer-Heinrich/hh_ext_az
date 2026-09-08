<?php
declare(strict_types=1);

$ll = 'LLL:EXT:hh_ext_az/Resources/Private/Language/locallang_db.xlf:';
$llCore = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
$llTabs = 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:';

$commonTabs = '
    --div--;' . $llTabs . 'categories,
        categories,
    --div--;' . $llTabs . 'language,
        --palette--;;language,
    --div--;' . $llTabs . 'access,
        --palette--;;visibility,
        --palette--;;access,
        --linebreak--,
        fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,';

return [
    'ctrl' => [
        'title' => $ll . 'tx_hhextaz_domain_model_entry',
        'label' => 'title',
        'descriptionColumn' => 'notes',
        'hideAtCopy' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'default_sortby' => 'title ASC',
        'type' => 'record_type',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'searchFields' => 'uid,title,teaser,description',
        'iconfile' => 'EXT:hh_ext_az/Resources/Public/Icons/Extension.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'palettes' => [
        'visibility' => [
            'showitem' => 'hidden, --linebreak--, starttime, endtime',
        ],
        'language' => [
            'showitem' => 'sys_language_uid, l10n_parent',
        ],
    ],
    'types' => [
        'default' => [
            'showitem' => 'record_type, title, slug, teaser, description, image,' . $commonTabs,
        ],
        'internal' => [
            'showitem' => 'record_type, title, teaser, internal_link,' . $commonTabs,
        ],
        'external' => [
            'showitem' => 'record_type, title, teaser, external_link,' . $commonTabs,
        ],
    ],
    'columns' => [
        'categories' => [
            'exclude' => true,
            'config' => [
                'type' => 'category',
            ],
        ],
        'fe_group' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login', 'value' => -1],
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login', 'value' => -2],
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups', 'value' => '--div--'],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
            ],
        ],
        'record_type' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.record_type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => $ll . 'tx_hhextaz_domain_model_entry.record_type.default',
                        'value' => 'default',
                    ],
                    [
                        'label' => $ll . 'tx_hhextaz_domain_model_entry.record_type.internal',
                        'value' => 'internal',
                    ],
                    [
                        'label' => $ll . 'tx_hhextaz_domain_model_entry.record_type.external',
                        'value' => 'external',
                    ],
                ],
                'default' => 'default',
            ],
        ],
        'title' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'slug' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title'],
                    'replacements' => [
                        '/' => '-',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'teaser' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 3,
            ],
        ],
        'description' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.description',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 8,
                'enableRichtext' => true,
            ],
        ],
        'image' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.image',
            'config' => [
                'type' => 'file',
                'maxitems' => 1,
                'allowed' => 'common-image-types',
            ],
        ],
        'internal_link' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.internal_link',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['page'],
                'required' => true,
            ],
        ],
        'external_link' => [
            'label' => $ll . 'tx_hhextaz_domain_model_entry.external_link',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['url'],
                'required' => true,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => $llCore . 'LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => $llCore . 'LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => $llCore . 'LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
            ],
        ],
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $llCore . 'LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => $llCore . 'LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => 'tx_hhextaz_domain_model_entry',
                'foreign_table_where' => 'AND {#tx_hhextaz_domain_model_entry}.{#pid}=###CURRENT_PID### AND {#tx_hhextaz_domain_model_entry}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
