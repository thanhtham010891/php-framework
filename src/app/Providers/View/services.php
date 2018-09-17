<?php


return [

    \App\Core\Contract\ViewManagerInterface::class => function ($settings) {
        return new App\Providers\View\Manager($settings['views']);
    }
];