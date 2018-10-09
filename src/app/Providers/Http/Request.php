<?php

namespace App\Providers\Http;


use System\Contract\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request implements RequestInterface
{
    private $symfonyRequest = [];

    public function __construct()
    {
        $this->symfonyRequest = SymfonyRequest::createFromGlobals();
    }

    public function isRequestGet()
    {
        return $this->symfonyRequest->isMethod('GET');
    }

    public function isRequestPost()
    {
        return $this->symfonyRequest->isMethod('POST');
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