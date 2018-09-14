<?php


return [

    \App\Core\Contract\ViewInterface::class => function ($settings) {
        return new App\Providers\View\View($settings);
    }
];