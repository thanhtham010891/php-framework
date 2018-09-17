<?php

$dir = dirname(__FILE__);
$ds = DIRECTORY_SEPARATOR;

return [

    [
        'name' => 'Route providers',
        'status' => true,
        'settings' => $dir . $ds . 'Route' . $ds . 'settings.php',
        'services' => $dir . $ds . 'Route' . $ds . 'services.php',
    ],

//    [
//        'name' => 'Database providers',
//        'status' => true,
//        'settings' => $dir . $ds . 'Database' . $ds . 'settings.php',
//        'services' => $dir . $ds . 'Database' . $ds . 'services.php',
//    ],
//
//    [
//        'name' => 'View providers',
//        'status' => true,
//        'settings' => $dir . $ds . 'View' . $ds . 'settings.php',
//        'services' => $dir . $ds . 'View' . $ds . 'services.php',
//    ]
];