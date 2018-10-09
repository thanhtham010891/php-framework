<?php
define('__ROOT__', dirname(__DIR__));


use System\Application;
use System\BaseException;

require_once __ROOT__ . '/vendor/autoload.php';

try {

    /**
     * Init application
     * Should be set instance of app for helpers
     */
    $app = Application::instance(
        new Application([
            'development' => true,
            'base_dir' => __ROOT__,
        ])
    );

    /**
     * Register service repository
     *
     * Let's check it at config/app.php and config/providers.php
     */
    $app->registerServiceProvider();

    /**
     * Listening httpRequest
     */
    $app->runHttp();

    /**
     * Terminate all services
     */
    $app->terminateServiceProvider();

} catch (BaseException $e) {
    Application::instance()->catchErrors($e);
}