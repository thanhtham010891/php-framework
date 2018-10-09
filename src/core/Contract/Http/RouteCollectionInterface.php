<?php

namespace System\Contract\Http;

interface RouteCollectionInterface
{
    /**
     * @return array
     */
    public function getResources();

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getRoute(RequestInterface $request);
}