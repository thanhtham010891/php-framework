<?php

namespace App\Providers\Http;

use System\BaseException;

use System\Contract\Http\RequestInterface;
use System\Contract\Http\RouteCollectionInterface;
use System\Contract\Http\RouteInterface;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var array
     */
    private $route = [];

    /**
     * @return array|mixed
     * @throws BaseException
     */
    public function getResources()
    {
        return require_path(app_path() . 'routes.php');
    }

    /**
     * @param RequestInterface $request
     * @return RouteInterface
     * @throws BaseException
     */
    public function getRoute(RequestInterface $request)
    {
        $args = [];

        $path = '/' . trim($request->getPath(), '/');

        $routes = $this->getResources();

        if (!empty($routes[$path])) {

            $this->route = $routes[$path];

        } else {

            foreach ($routes as $pattern => $routeResource) {

                if (preg_match_all('#^' . trim($pattern, '#') . '$#Usm', $path, $args)) {
                    $this->route = $routeResource;
                    break;
                }
            }
        }

        if (is_string($this->route)) {
            return new Route([
                'require' => $this->route
            ]);
        }

        if (!is_array($this->route)) {
            throw new BaseException('Route item must be an array');
        }

        if (empty($this->route['controller'])) {
            throw new BaseException('Route item[controller] is required');
        }

        if (!class_exists($this->route['controller'])) {
            throw new BaseException('Controller "' . $this->route['controller'] . '" does not exist');
        }

        if (empty($this->route['method'])) {
            throw new BaseException('Route item[method] is required');
        }

        if (!empty($args[1])) {
            unset($args[0]);
        }

        $this->route['args'] = $args;

        return new Route($this->route);
    }
}