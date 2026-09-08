<?php

$EM_CONF['hh_ext_az'] = [
    'title' => 'Hauer-Heinrich - A-Z',
    'description' => 'A-Z Verzeichnis (z. B. Satzungen und Verordnungen) mit alphabetischer Liste, Detailseite, Volltextsuche (Highlighting) und Sprungmenü',
    'category' => 'plugin',
    'author' => 'Christian Hackl',
    'author_email' => 'chackl@hauer-heinrich.de',
    'author_company' => 'www.hauer-heinrich.de',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhExtAz\\' => 'Classes/',
        ],
    ],
];
