<?php

namespace App\Core\Contract;

interface ViewInterface extends ServiceInterface
{
    /**
     * @param string $path
     */
    public function setViewPath($path);

    /**
     * @return string
     */
    public function getViewPath();

    /**
     * @param mixed|array $resource
     * @return void
     */
    public function setResource($resource);


    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response);

    /**
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * @return void
     */
    public function run();
}