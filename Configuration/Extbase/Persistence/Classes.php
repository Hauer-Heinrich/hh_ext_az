<?php
declare(strict_types=1);

use HauerHeinrich\HhExtAz\Domain\Model\DefaultEntry;
use HauerHeinrich\HhExtAz\Domain\Model\Entry;
use HauerHeinrich\HhExtAz\Domain\Model\ExternalEntry;
use HauerHeinrich\HhExtAz\Domain\Model\InternalEntry;
use HauerHeinrich\HhExtAz\Domain\Model\FileEntry;

/*
 * Single Table Inheritance:
 * Eine Tabelle (tx_hhextaz_domain_model_entry), mehrere Models je record_type.
 */
return [
    Entry::class => [
        'tableName' => 'tx_hhextaz_domain_model_entry',
        'subclasses' => [
            'default' => DefaultEntry::class,
            'internal' => InternalEntry::class,
            'external' => ExternalEntry::class,
            'file' => FileEntry::class,
        ],
    ],
    DefaultEntry::class => [
        'tableName' => 'tx_hhextaz_domain_model_entry',
        'recordType' => 'default',
    ],
    InternalEntry::class => [
        'tableName' => 'tx_hhextaz_domain_model_entry',
        'recordType' => 'internal',
    ],
    ExternalEntry::class => [
        'tableName' => 'tx_hhextaz_domain_model_entry',
        'recordType' => 'external',
    ],
    FileEntry::class => [
        'tableName' => 'tx_hhextaz_domain_model_entry',
        'recordType' => 'file',
    ],
];
