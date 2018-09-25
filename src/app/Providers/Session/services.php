<?php

return [
    \App\Core\Contract\SessionInterface::class => function ($settings) {
        return new \App\Proviers\Services\Session($settings);
    }
];