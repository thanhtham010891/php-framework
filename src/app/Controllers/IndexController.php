<?php

namespace App\Controllers;

use App\Providers\View\Type\Web;

class IndexController extends Web
{
    public function index()
    {
        return [
            'render' => 'index.html',
            'data' => ['name' => 'thamtt', 'email' => 'thamtt@gmail.com']
        ];
    }
}