<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Contract\Controllers\WebInterface;

use App\Providers\View\Type\WebTrait;

class IndexController extends Controller implements WebInterface
{
    use WebTrait;

    public function index()
    {
        return [
            'render' => 'index.html',
            'data' => ['name' => 'thamtt', 'email' => 'thamtt@gmail.com']
        ];
    }
}