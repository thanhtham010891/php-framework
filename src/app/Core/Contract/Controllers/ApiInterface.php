<?php

namespace App\Core\Contract\Controllers;

interface ApiInterface
{
    /**
     * @param array $data
     * @param string $message
     * @return mixed
     */
    public function success(array $data, $message = "");

    /**
     * @param array $data
     * @param string $message
     * @return mixed
     */
    public function fails(array $data, $message = "");

    /**
     * @return void
     */
    public function send();
}