<?php

return [
    'views' => [
        'path' => dirname(__FILE__) . '/src',
        'cache' => [
            'status' => true,
            'path' => storage_path() . 'views',
        ],
        'page_404' => dirname(__FILE__) . '/src/404.php'
    ]
];