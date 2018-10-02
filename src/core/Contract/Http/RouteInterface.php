<?php

namespace System\Contract\Http;

interface RouteInterface
{
    /**
     * @return array
     */
    public function getResource();

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getControllerResource(RequestInterface $request);

    /**
     * @param string|array $notFoundResource
     */
    public function setNotFoundResource($notFoundResource);

    /**
     * @return string|array
     */
    public function getNotFoundResource();
}