<?php

namespace App\Controllers\Api;

use App\Providers\View\Type\Api;

class V1 extends Api
{

    public function index()
    {
        return $this->success([
            'name' => 'thamtt',
            'email' => 'thamtt@nal.vn'
        ]);
    }
}