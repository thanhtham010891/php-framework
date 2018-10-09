<?php

namespace App\Providers\Http;

use App\Providers\Http\Exception\RouteException;

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
     * @var array
     */
    private $getResource = [];

    /**
     * @var array
     */
    private $postResource = [];


    /**
     * @param string $path
     * @param array|string $resource
     * @return $this|RouteCollectionInterface
     */
    public function get($path, $resource)
    {
        $this->registerRoute('GET', $path, $resource);

        return $this;
    }

    /**
     * @param string $path
     * @param array|string $resource
     * @return $this|RouteCollectionInterface
     */
    public function post($path, $resource)
    {
        $this->registerRoute('POST', $path, $resource);

        return $this;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|string $resource
     */
    public function registerRoute($method, $path, $resource)
    {
        $method = strtoupper($method);

        $path = '/' . trim($path, '/');

        if ($method === 'GET') {
            $this->getResource[$path] = $resource;
        } elseif ($method === 'POST') {
            $this->postResource[$path] = $resource;
        }
    }

    /**
     * @param RequestInterface $request
     * @return RouteInterface
     * @throws RouteException
     */
    public function getRoute(RequestInterface $request)
    {
        require_once(app_path() . 'routes.php');

        if ($request->isRequestPost()) {
            $routes = $this->postResource;
        } else {
            $routes = $this->getResource;
        }

        $args = [];

        $path = '/' . trim($request->getPath(), '/');

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
            throw new RouteException('Route item must be an array');
        }

        if (empty($this->route['controller'])) {
            throw new RouteException('Route item[controller] is required');
        }

        if (!class_exists($this->route['controller'])) {
            throw new RouteException('Controller "' . $this->route['controller'] . '" does not exist');
        }

        if (empty($this->route['method'])) {
            throw new RouteException('Route item[method] is required');
        }

        if (!empty($args[1])) {
            unset($args[0]);
        }

        $this->route['args'] = $args;

        return new Route($this->route);
    }
}