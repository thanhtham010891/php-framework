<?php

namespace App\Providers\View\Type;

use App\Core\Contract\ResponseInterface;
use App\Core\Controller;
use App\Core\Contract\Controllers\ApiInterface;

class Api extends Controller implements ApiInterface
{

    /**
     * @var array
     */
    private $responseData;

    public function success(array $data, $message = "")
    {
        if (empty($message)) {
            $message = "OK";
        }

        $this->responseData = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public function fails(array $data, $message = "")
    {
        if (empty($message)) {
            $message = "Error";
        }

        $this->responseData = [
            'status' => false,
            'message' => $message,
            'data' => $data
        ];
    }

    public function send()
    {
        /**
         * @var ResponseInterface $response
         */
        $response = $this->services[ResponseInterface::class];

        $response->prepare(
            json_encode($this->responseData), 200, ['Content-type' => 'application/json']
        );

        return $response->send();
    }
}