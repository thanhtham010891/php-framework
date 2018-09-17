<?php

namespace App\Core;

use App\Core\Contract\Controllers\ApiInterface;
use App\Core\Contract\Controllers\DispatchInterface;
use App\Core\Contract\Controllers\WebInterface;
use App\Core\Contract\ServiceInterface;
use App\Core\Contract\RouteInterface;
use App\Core\Contract\RequestInterface;
use App\Core\Contract\ResponseInterface;
use App\Core\Contract\SessionInterface;

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
         * Providers settings resources
         */
        'providers' => [
            'path' => '/app/Providers'
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

    public function getStoragePath()
    {
        return $this->getBaseDir() . rtrim($this->settings['storage']['path'], self::DS) . self::DS;
    }

    /**
     * @return string
     */
    public function getMainFile()
    {
        return $this->settings['__MAIN__'];
    }

    public function isDevelop()
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
        if ($this->isDevelop()) {
            ini_set('display_errors', true);
            ini_set('error_reporting', E_ALL);
        }

        $this->_registerBaseServices();

        $this->_registerExternalService();

        $controllerResources = $this->_getControllerResource(
            $this->getService(RouteInterface::class), $this->getService(RequestInterface::class)
        );

        if (!empty($controllerResources)) {

            $controllerObject = new $controllerResources['controller'];

            /**
             * Bootstrap controller
             */
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

        } else {
            throw new ApplicationException('Controller resource is not registered');
        }

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
     */
    private function _registerExternalService()
    {
        foreach (require_once($this->getProviderPath() . 'bootstrap.php') as $provider) {

            if (empty($provider['status'])) {
                continue;
            }

            if (file_exists($provider['settings'])) {
                $this->services->settings(require_once($provider['settings']));
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