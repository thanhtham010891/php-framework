<?php
define('__ROOT__', dirname(__DIR__));


use System\Application;
use System\BaseException;

require_once __ROOT__ . '/vendor/autoload.php';

try {

    /**
     * Init application
     */
    Application::instance(
        new Application([
            '__MAIN__' => __FILE__,
            'development' => true,
            'base_dir' => __ROOT__,
        ])
    )->run();

} catch (BaseException $e) {
    Application::instance()->errors($e);
}