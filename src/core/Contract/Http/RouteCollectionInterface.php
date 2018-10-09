<?php

namespace System\Contract\Http;

interface RouteCollectionInterface
{

    /**
     * @param string $method
     * @param string $path
     * @param string|array $resource
     * @return void
     */
    public function registerRoute($method, $path, $resource);

    /**
     * @param string $path
     * @param string|array $resource
     * @return $this
     */
    public function get($path, $resource);

    /**
     * @param string $path
     * @param string|array $resource
     * @return $this
     */
    public function post($path, $resource);

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getRoute(RequestInterface $request);
}