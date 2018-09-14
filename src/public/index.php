<?php define('ROOT', dirname(__DIR__));

use App\Core\Application;
use App\Exceptions\ApplicationException;

require_once ROOT . '/vendor/autoload.php';

try {

    /**
     * Init application
     */
    Application::instance(
        new Application(require ROOT . '/config/settings.php')
    )->run();

} catch (ApplicationException $e) {
    echo $e->getMessage();
}