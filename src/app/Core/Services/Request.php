<?php

namespace App\Core\Services;

use App\Core\Contract\RequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request implements RequestInterface
{
    private $symfonyRequest = [];

    public function __construct()
    {
        $this->symfonyRequest = SymfonyRequest::createFromGlobals();
    }

    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }

    public function terminate()
    {
        // TODO: Implement terminate() method.
    }

    public function getAllQueryParam()
    {
        return $this->symfonyRequest->query->all();
    }

    public function getParam($key, $default = '')
    {
        return $this->symfonyRequest->get($key, $default);
    }

    public function toJson()
    {
        return json_encode($this->getAllQueryParam());
    }

    public function getPath()
    {
        return $this->symfonyRequest->getPathInfo();
    }

    public function getQueryString()
    {
        return $this->symfonyRequest->getQueryString();
    }
}