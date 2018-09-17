<?php

use App\Providers\Database\Database;
use App\Providers\Database\PDOConnection;

return [

    \App\Core\Contract\Database\DatabaseInterface::class => function ($settings) {
        return new Database(new PDOConnection($settings['models']['connection']));
    }
];