<?php

return [

    \App\Core\Contract\RouteInterface::class => function () {

        return new \App\Providers\Route\Route();
    }

];