<?php

$ds = DIRECTORY_SEPARATOR;

return [
    'views' => [
        'path' => dirname(__FILE__) . $ds . 'src',
        'page_404' => dirname(__FILE__) . $ds . 'src' . $ds . '404.php'
    ]
];