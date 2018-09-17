<?php

return [

    [
        'name' => 'Route providers',
        'status' => true,
        'settings' => $this->getProviderPath() . 'Route/settings.php',
        'services' => $this->getProviderPath() . 'Route/services.php',
    ],
    [
        'name' => 'View providers',
        'status' => true,
        'settings' => $this->getProviderPath() . 'View/settings.php',
        'services' => $this->getProviderPath() . 'View/services.php',
    ]
];