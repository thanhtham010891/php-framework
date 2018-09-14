<?php

namespace App\Core;

use App\Core\Contract\RouteInterface;
use App\Core\Contract\ServiceInterface;
use App\Core\Contract\SessionInterface;
use App\Core\Contract\ViewInterface;
use App\Core\Contract\RequestInterface;
use App\Core\Contract\ResponseInterface;

use App\Exceptions\ApplicationException;

use App\Core\Services\Request;
use App\Core\Services\Response;
use App\Core\Services\Session;

/**
 * Class Application
 * @author thamtt@nal.vn
 * @package App\Core
 */
class Application
{

    const DS = DIRECTORY_SEPARATOR;

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

        /**
         * Base dir
         */
        'base_dir' => '/var/www/html',

        /**
         * Model settings resources
         */
        'models' => [
            'connection' => [
                'hostname' => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'db_name' => 'db_name',
                'options' => []
            ]
        ],

        /**
         * Providers settings resources
         */
        'providers' => [
            'path' => '/app/Providers'
        ]

    ];

    /**
     * Application services
     * Initialize service contract
     *
     * @var array
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
        return $this->services;
    }

    /**
     * @param string $contract
     * @return mixed
     * @throws ApplicationException
     */
    public function getService($contract)
    {
        if (empty($this->services[$contract])) {
            throw new ApplicationException('Services "' . $contract . '" is not registered');
        }

        return $this->services[$contract];
    }

    /**
     * @param string $contract
     * @param mixed $service
     *
     * @throws ApplicationException
     */
    public function setService($contract, $service)
    {
        if (!interface_exists($contract)) {
            throw new ApplicationException('Interface "' . $contract . '" does not exist');
        }

        if (is_callable($service)) {
            $service = $service($this->settings);
        }

        if ($service instanceof $contract) {
            $this->services[$contract] = $service;
        } else {
            throw new ApplicationException(
                'Services "' . get_class($service) . '" must be implemented by ' . $this->services[$contract]
            );
        }
    }

    /**
     * @param array $settings
     */
    public function settings($settings)
    {
        $this->settings = array_replace($this->settings, $settings);
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return rtrim($this->settings['base_dir'], self::DS) . self::DS;
    }

    /**
     * @return string
     */
    public function getProviderPath()
    {
        return $this->getBaseDir() . rtrim($this->settings['providers']['path'], self::DS) . self::DS;
    }

    /**
     * @return string
     */
    public function getMainFile()
    {
        return $this->settings['__MAIN__'];
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
    }

    /**
     * @throws ApplicationException
     */
    public function run()
    {
        if ($this->settings['development']) {
            ini_set('display_errors', true);
            ini_set('error_reporting', E_ALL);
        }

        $this->_registerBaseServices();

        $this->_registerExternalService();

        /**
         * Bootstrap services
         */
        foreach ($this->getAllService() as $service) {
            /**
             * @var ServiceInterface $service
             */
            if ($service instanceof ServiceInterface) {
                $service->bootstrap();
            }
        }

        /**
         * @var RequestInterface $request
         * @var ResponseInterface $response
         * @var ViewInterface $view
         * @var RouteInterface $view
         */
        $request = $this->getService(RequestInterface::class);
        $response = $this->getService(ResponseInterface::class);
        $route = $this->getService(RouteInterface::class);
        $view = $this->getService(ViewInterface::class);

        $controllerResource = $this->_getControllerResource($route, $request);

        $view->setResponse($this->getService(ResponseInterface::class));

        if (!empty($controllerResource)) {

            $view->setResource(call_user_func_array(
                [new $controllerResource['controller'], $controllerResource['method']],
                [$request, $response, $controllerResource['args']]
            ));
        }

        $view->run();

        /**
         * Terminate services
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
     * @param RouteInterface $route
     * @param RequestInterface $request
     * @return array
     * @throws ApplicationException
     */
    private function _getControllerResource(RouteInterface $route, RequestInterface $request)
    {
        $resource = $args = [];

        $path = '/' . trim($request->getPath(), '/');

        $routes = $route->getResource();

        if (!empty($routes[$path])) {

            $resource = $routes[$path];

        } else {

            foreach ($routes as $pattern => $routeResource) {

                if (preg_match_all('#^' . trim($pattern, '#') . '$#Usm', $path, $args)) {
                    $resource = $routeResource;
                    break;
                }
            }
        }

        if (empty($resource)) {
            return [];
        }

        if (!is_array($resource)) {
            throw new ApplicationException('Route item must be an array');
        }

        if (empty($resource['controller'])) {
            throw new ApplicationException('Route item[controller] is required');
        }

        if (!class_exists($resource['controller'])) {
            throw new ApplicationException('Controller "' . $resource['controller'] . '" does not exist');
        }

        if (empty($resource['method'])) {
            throw new ApplicationException('Route item[method] is required');
        }

        if (empty($resource['name'])) {
            $resource['name'] = $resource['controller'] . '.' . $resource['method'];
        }

        $resource['args'] = $args;

        return $resource;
    }

    /**
     * Register system core services
     *
     * @throws ApplicationException
     */
    private function _registerBaseServices()
    {
        $this->setService(RequestInterface::class, function () {
            return new Request();
        });

        $this->setService(ResponseInterface::class, function () {
            return new Response();
        });

        $this->setService(SessionInterface::class, function ($settings) {
            return new Session($settings);
        });
    }

    /**
     * Register external services
     *
     * @throws ApplicationException
     */
    private function _registerExternalService()
    {

        foreach (require_once($this->getProviderPath() . 'bootstrap.php') as $provider) {

            if (empty($provider['status'])) {
                continue;
            }

            if (file_exists($provider['settings'])) {

                $this->settings(require_once($provider['settings']));
            }

            if (file_exists($provider['services'])) {

                $services = require_once($provider['services']);

                foreach ($services as $contract => $service) {
                    $this->setService($contract, $service);
                }
            }
        }
    }
}