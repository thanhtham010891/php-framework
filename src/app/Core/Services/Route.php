<?php

namespace App\Core\Services;

use App\Exceptions\ApplicationException;

use App\Core\Contract\RequestInterface;
use App\Core\Contract\RouteInterface;

class Route implements RouteInterface
{

    /**
     * @var array
     */
    private $resource = [];

    /**
     * @return array|mixed
     * @throws ApplicationException
     */
    public function getResource()
    {
        return require_path(app_path() . 'routes.php');
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws ApplicationException
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
            return [];
        }

        if (is_string($this->resource)) {
            return [
                'require' => $this->resource
            ];
        }

        if (!is_array($this->resource)) {
            throw new ApplicationException('Route item must be an array');
        }

        if (empty($this->resource['controller'])) {
            throw new ApplicationException('Route item[controller] is required');
        }

        if (!class_exists($this->resource['controller'])) {
            throw new ApplicationException('Controller "' . $this->resource['controller'] . '" does not exist');
        }

        if (empty($this->resource['method'])) {
            throw new ApplicationException('Route item[method] is required');
        }

        if (empty($this->resource['name'])) {
            $resource['name'] = $this->resource['controller'] . '.' . $this->resource['method'];
        }

        $this->resource['args'] = $args;

        return $this->resource;
    }
}