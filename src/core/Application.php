<?php

namespace System;

use Exception;

use System\Contract\Http\RouteInterface;
use System\Contract\ServiceInterface;

use System\Contract\Http\RouteCollectionInterface;
use System\Contract\Http\RequestInterface;

use System\Contract\Controllers\ApiInterface;
use System\Contract\Controllers\WebInterface;
use System\Contract\Controllers\DispatchInterface;

include_once('helpers/path.php');

/**
 * Class Application
 *
 * @author thamtt@nal.vn
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

        'base_dir' => '/var/www/html',

        'public' => [
            'path' => 'public',
        ],

        'app' => [
            'path' => 'app',
        ],

        'config' => [
            'path' => 'config',
        ],

        /**
         * Providers settings resources
         */
        'providers' => [
            'path' => 'app/Providers',
        ],

        /**
         * Providers resource settings
         */
        'storage' => [
            'path' => 'storage',
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
     * @throws BaseException
     */
    public function runHttp()
    {
        if ($this->isDevelopMode()) {

            ini_set('display_errors', true);
            ini_set('error_reporting', E_ALL);

            set_exception_handler([$this, 'catchException']);
        }

        /**
         * @var RouteCollectionInterface $routeCollection
         * @var RouteInterface $route
         */
        $routeCollection = $this->getService(RouteCollectionInterface::class);

        $route = $routeCollection->getRoute(
            $this->getService(RequestInterface::class)
        );

        /**
         * Using for require static page or restart new an another application
         */
        if ($route->getRequire()) {

            $this->terminateServiceProvider();

            require_path($route->getRequire());

            exit;
        }

        /**
         * Bootstrap controller
         */
        $controllerObject = $route->getController();

        if ($controllerObject instanceof DispatchInterface) {
            $controllerObject->dispatch($this->services);
        }

        /**
         * Controller is running
         */
        $responseData = call_user_func_array(
            [$controllerObject, $route->getMethod()], $route->getParams()
        );

        if ($controllerObject instanceof WebInterface) {
            $controllerObject->render((array)$responseData);
        } elseif ($controllerObject instanceof ApiInterface) {
            $controllerObject->send();
        }
    }

    /**
     * @throws BaseException
     */
    public function registerServiceProvider()
    {
        $this->services->settings(
            (array)require_path(config_path() . 'app.php')
        );

        foreach ((array)require_path(config_path() . 'providers.php') as $contract => $service) {
            $this->setService($contract, $service);
        }
    }

    public function terminateServiceProvider()
    {
        /**
         * Terminate all service
         */
        foreach ($this->getAllService() as $contract => $service) {
            /**
             * @var ServiceInterface $service
             */
            if ($service instanceof ServiceInterface) {
                $service->terminate();
            }

            unset($this->services[$contract]);
        }
    }

    /**
     * @param $e
     */
    public function catchException($e)
    {
        /**
         * @var Exception $e
         */
        if ($this->isDevelopMode()) {
            echo get_class($e) . ': ' . $e->getMessage();
            echo '<pre>', $e->getTraceAsString(), '</pre>';
        } else {
            echo 'Error!';
        }
    }
}