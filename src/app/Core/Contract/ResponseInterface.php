<?php

namespace App\Core\Contract;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface ResponseInterface extends ServiceInterface
{
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function prepare($content = '', $status = 200, array $headers = array());

    /**
     * @return SymfonyResponse
     */
    public function send();
}