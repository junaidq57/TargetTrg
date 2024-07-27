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
                'dbname' => 'admin_stage',
                'username' => 'admin_stage',
                'password' => 'Z0y2~9xn1',
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
        'dbname' => 'dev_wp',
        'username' => 'dev_wp',
        'password' => 'Pb9cb02~9',
        'active' => '1'
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'fishpig_wordpress' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'vertex' => 1,
        'google_product' => 1
    ],
    'install' => [
        'date' => 'Fri, 22 Dec 2023 02:42:15 +0000'
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
    'downloadable_domains' => [
        'targettrg.co.uk'
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => 'uRS0WMHsj4cW3IQUzxkqYL5deYpMKIEv'
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => 'c6e_'
            ],
            'page_cache' => [
                'id_prefix' => 'c6e_'
            ]
        ],
        'allow_parallel_generation' => false
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
