<?php
return [

    'views' => [
        'path' => provider_path() . 'View/src',
        'cache' => [
            'status' => true,
            'path' => storage_path() . 'views',
        ],
        'page_404' => provider_path() . 'View/src/404.php'
    ],

    /**
     * Model settings resources
     */
    'models' => [
        'driver' => 'mysql',

        'connection' => [
            'hostname' => 'mysql.local',
            'username' => 'root',
            'password' => 'root',
            'db_name' => 'test',
            'options' => []
        ]
    ],
];