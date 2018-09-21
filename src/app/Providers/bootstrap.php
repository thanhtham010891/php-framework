<?php

return [

    [
        'name' => 'Route providers',
        'status' => true,
        'settings' => provider_path() . 'Route/settings.php',
        'services' => provider_path() . 'Route/services.php',
    ],
    [
        'name' => 'View providers',
        'status' => true,
        'settings' => provider_path() . 'View/settings.php',
        'services' => provider_path() . 'View/services.php',
    ],

    [
        'name' => 'Database providers',
        'status' => true,
        'settings' => provider_path() . 'Database/settings.php',
        'services' => provider_path() . 'Database/services.php',
    ]
];