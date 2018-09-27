<?php

namespace App\Providers\Http;

use System\BaseException;

use System\Contract\Http\RequestInterface;
use System\Contract\Http\RouteInterface;

class Route implements RouteInterface
{

    /**
     * @var array
     */
    private $resource = [];

    /**
     * @var array
     */
    private $notFoundResource = [];

    /**
     * @return array|mixed
     * @throws BaseException
     */
    public function getResource()
    {
        return require_path(app_path() . 'routes.php');
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws BaseException
     */
    public function getControllerResource(RequestInterface $request)
    {
        if (!empty($this->resource)) {
            return $this->resource;
        }

        $args = [];

        $path = '/' . trim($request->getPath(), '/');

        $routes = $this->getResource();

        if (!empty($routes[$path])) {

            $this->resource = $routes[$path];

        } else {

            foreach ($routes as $pattern => $routeResource) {

                if (preg_match_all('#^' . trim($pattern, '#') . '$#Usm', $path, $args)) {
                    $this->resource = $routeResource;
                    break;
                }
            }
        }

        if (empty($this->resource)) {
            return $this->getNotFoundResource();
        }

        if (is_string($this->resource)) {
            return [
                'require' => $this->resource
            ];
        }

        if (!is_array($this->resource)) {
            throw new BaseException('Route item must be an array');
        }

        if (empty($this->resource['controller'])) {
            throw new BaseException('Route item[controller] is required');
        }

        if (!class_exists($this->resource['controller'])) {
            throw new BaseException('Controller "' . $this->resource['controller'] . '" does not exist');
        }

        if (empty($this->resource['method'])) {
            throw new BaseException('Route item[method] is required');
        }

        if (empty($this->resource['name'])) {
            $resource['name'] = $this->resource['controller'] . '.' . $this->resource['method'];
        }

        $this->resource['args'] = $args;

        return $this->resource;
    }

    /**
     * @param string|array $notFoundResource
     */
    public function setNotFoundResource($notFoundResource)
    {
        $this->notFoundResource = $notFoundResource;
    }

    /**
     * @return string|array
     */
    public function getNotFoundResource()
    {
        return $this->notFoundResource;
    }
}