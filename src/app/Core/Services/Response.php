<?php

namespace App\Core\Services;

use App\Core\Contract\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response implements ResponseInterface
{
    /**
     * @var SymfonyResponse
     */
    private $response;

    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }

    public function terminate()
    {
        // TODO: Implement terminate() method.
    }

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