<?php
$EM_CONF['cron_sluggy'] = [
    'title' => 'Slug regenerator',
    'description' => 'Regenerate Slugs',
    'category' => 'module',
    'author' => 'Ernesto Baschny',
    'author_email' => 'eb@cron.eu',
    'state' => 'stable',
    'createDirs' => '',
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
