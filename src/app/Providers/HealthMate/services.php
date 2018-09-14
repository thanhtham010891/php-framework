<?php

return [

    \App\Providers\HealthMate\ApiInterface::class => function ($settings) {

        $clientRequest = new \GuzzleHttp\Client();

        return new \App\Providers\HealthMate\Api(
            $settings['health_mate']['clientId'],
            $settings['health_mate']['secretId'],
            $clientRequest
        );
    }
];