<?php
define('__ROOT__', dirname(__DIR__));

use App\Core\Application;
use App\Exceptions\ApplicationException;

require_once __ROOT__ . '/vendor/autoload.php';

try {

    /**
     * Init application
     */
    Application::instance(
        new Application([
            'domain' => 'http://thamtt.local',
            '__MAIN__' => __FILE__,
            'development' => true,
            'base_dir' => __ROOT__,
        ])
    )->run();

} catch (ApplicationException $e) {
    Application::instance()->errors($e);
}