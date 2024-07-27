<?php
return [
    'backend' => [
        'frontName' => 'secure_admin'
    ],
    'crypt' => [
        'key' => 'e709d03d9721c228d44bf778c12e28b1'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'targ321ettrg',
                'username' => 'targ321ettrg',
                'password' => '#jJ7q0n5',
                'active' => '1',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'wordpress' => [
        'host' => 'localhost',
        'dbname' => 'targ321ettrgwp',
        'username' => 'targ321ettrgwp',
        'password' => '0#3G~o16',
        'active' => '1'
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'fishpig_wordpress' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 0,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'vertex' => 1,
        'google_product' => 1
    ],
    'install' => [
        'date' => 'Sun, 31 Dec 2023 09:53:14 +0000'
    ],
    'system' => [
        'default' => [
            'dev' => [
                'debug' => [
                    'debug_logging' => '0'
                ]
            ]
        ]
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'c6e_'
            ],
            'page_cache' => [
                'id_prefix' => 'c6e_'
            ]
        ],
        'graphql' => [
            'id_salt' => 'P57eFDmPLzDP05XCdDgVdCua5l1YDUtQ'
        ],
        'allow_parallel_generation' => false
    ],
    'downloadable_domains' => [
        'targettrg.co.uk'
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ]
];
