<?php

namespace App\Core;

use App\Core\Contract\Controllers\ApiInterface;
use App\Core\Contract\Controllers\DispatchInterface;
use App\Core\Contract\Controllers\WebInterface;
use App\Core\Contract\ServiceInterface;
use App\Core\Contract\RouteInterface;
use App\Core\Contract\RequestInterface;
use App\Core\Contract\ResponseInterface;

use App\Core\Services\Route;
use App\Exceptions\ApplicationException;

use App\Core\Services\Request;
use App\Core\Services\Response;

include_once('helpers/path.php');

/**
 * Class Application
 *
 * @author thamtt@nal.vn
 * @package App\Core
 */
class Application
{

    /**
     * Application settings
     *
     * @var array
     */
    private $settings = [

        /**
         * Self file name call application init
         */
        '__MAIN__' => 'index.php',

        /**
         * Disable this param when in development
         */
        'development' => false,

        'domain' => 'http://thamtt.local',

        'base_dir' => '/var/www/html',

        'public' => [
            'path' => 'public'
        ],

        'app' => [
            'path' => 'app'
        ],

        /**
         * Providers settings resources
         */
        'providers' => [
            'path' => 'app/Providers'
        ],

        /**
         * Providers resource settings
         */
        'storage' => [
            'path' => 'storage'
        ]
    ];

    /**
     * Application services
     * Initialize service contract
     *
     * @var ServiceRepository
     */
    private $services = [];

    /**
     * @var self
     */
    private static $instance;

    /**
     * @return array
     */
    public function getAllService()
    {
        return $this->services->toArray();
    }

    /**
     * @param string $contract
     * @return mixed
     */
    public function getService($contract)
    {
        return $this->services[$contract];
    }

    /**
     * @param string $contract
     * @param mixed $service
     */
    public function setService($contract, $service)
    {
        $this->services[$contract] = $service;
    }

    /**
     * @param array $settings
     */
    public function settings($settings)
    {
        $this->settings = array_replace($this->settings, $settings);
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return string
     */
    public function getMainFile()
    {
        return $this->settings['__MAIN__'];
    }

    /**
     * @return bool
     */
    public function isDevelopMode()
    {
        return in_array(
            strtolower($this->settings['development']), [true, 'true', 1, '1', 'develop', 'development', 'dev', 'local']
        );
    }

    /**
     * Application constructor.
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        if (!empty($settings)) {
            /**
             * Register settings
             */
            $this->settings($settings);
        }

        $this->services = new ServiceRepository($this->settings);
    }

    /**
     * @throws ApplicationException
     */
    public function run()
    {
        if ($this->isDevelopMode()) {
            ini_set('display_errors', true);
            ini_set('error_reporting', E_ALL);
        }

        $this->_registerBaseServices();

        $this->_registerExternalService();

        /**
         * @var RouteInterface $route
         */
        $route = $this->getService(RouteInterface::class);

        $controllerResources = $route->getControllerResource(
            $this->getService(RequestInterface::class)
        );

        if (empty($controllerResources)) {

            throw new ApplicationException('Controller resource is not registered');
        }

        /**
         * Using for require static page or restart new an another application
         */
        if (isset($controllerResources['require'])) {

            $this->_terminateAllService();

            require_path($controllerResources['require']);

            exit;
        }

        /**
         * Bootstrap controller
         */
        $controllerObject = new $controllerResources['controller'];

        if ($controllerObject instanceof DispatchInterface) {
            $controllerObject->dispatch($this->services);
        }

        /**
         * Controller is running
         */
        $responseData = call_user_func_array(
            [$controllerObject, $controllerResources['method']], $controllerResources['args']
        );

        if ($controllerObject instanceof WebInterface) {
            $controllerObject->render($responseData);
        } elseif ($controllerObject instanceof ApiInterface) {
            $controllerObject->send();
        }

        unset($controllerObject, $controllerResources);

        $this->_terminateAllService();
    }

    /**
     * @param Application $app
     * @return Application
     */
    public static function instance(Application $app = null)
    {
        if (!empty($app)) {
            self::$instance = $app;
        }

        return self::$instance;
    }

    /**
     * Register system core services
     */
    private function _registerBaseServices()
    {
        $this->setService(RequestInterface::class, function () {
            return new Request();
        });

        $this->setService(ResponseInterface::class, function () {
            return new Response();
        });

        $this->setService(RouteInterface::class, function () {
            return new Route();
        });
    }

    /**
     * @throws ApplicationException
     */
    private function _registerExternalService()
    {
        foreach (require_path(provider_path() . 'bootstrap.php') as $provider) {

            if (empty($provider['status'])) {
                continue;
            }

            if (file_exists($provider['settings'])) {
                $this->services->settings(require_path($provider['settings']));
            }

            if (file_exists($provider['services'])) {

                $services = require_path($provider['services']);

                foreach ($services as $contract => $service) {
                    $this->setService($contract, $service);
                }
            }
        }
    }

    private function _terminateAllService()
    {
        /**
         * Terminate all service
         */
        foreach ($this->getAllService() as $service) {
            /**
             * @var ServiceInterface $service
             */
            if ($service instanceof ServiceInterface) {
                $service->terminate();
            }
        }
    }
}