<?php

namespace App\Providers\Http;

use System\Contract\Http\RouteInterface;

class Route implements RouteInterface
{

    /**
     * @var array
     */
    private $route;

    public function __construct($route)
    {
        $this->route = $route;
    }

    public function getName()
    {
        return trim($this->route['controller']) . '.' . trim($this->route['method']);
    }

    public function getController()
    {
        return new $this->route['controller'];
    }

    public function getMethod()
    {
        return trim($this->route['method']);
    }

    public function getParams()
    {
        return $this->route['args'];
    }

    public function getRequire()
    {
        return $this->route['require'] ?? '';
    }
}