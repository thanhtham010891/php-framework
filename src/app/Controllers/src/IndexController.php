<?php

namespace App\Controllers\src;

use App\Core\Contract\RequestInterface;
use App\Core\Contract\ResponseInterface;

class IndexController
{

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->prepare(json_encode([
            'status' => true,
            'message' => 'Hello world',
            'version' => '1.0',
            'author' => 'thamtt@nal.vn'
        ]), 200, [
            'Content-type' => 'application/json'
        ]);
    }
}