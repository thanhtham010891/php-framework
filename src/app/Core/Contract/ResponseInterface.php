<?php

namespace App\Core\Contract;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface ResponseInterface
{
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return ResponseInterface
     */
    public function prepare($content = '', $status = 200, array $headers = array());

    /**
     * @return SymfonyResponse
     */
    public function send();
}