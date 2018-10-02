<?php

namespace App\Providers\Http;


use System\Contract\Http\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response implements ResponseInterface
{
    /**
     * @var SymfonyResponse
     */
    private $response;

    public function prepare($content = '', $status = 200, array $headers = array())
    {
        $this->response = SymfonyResponse::create($content, $status, $headers);

        return $this;
    }

    public function send()
    {
        return $this->response->send();
    }
}