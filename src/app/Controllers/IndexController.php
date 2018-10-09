<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Providers\Database\Model;
use System\Contract\Database\DatabaseInterface;
use System\Contract\Database\QueryBuilderInterface;
use System\Controller;

class IndexController extends Controller
{
    /**
     * Simple method
     */
    public function index()
    {
        echo 'Hello world';
    }

    /**
     * Regex routes
     *
     * @param $a
     * @param $b
     */
    public function regex($a, $b)
    {
        print_r($a);
        print_r($b);
    }

    public function db()
    {
        Model::registerGlobals($this->services[DatabaseInterface::class]);

        $user = new User();

        $user->setQueryBuilder($this->services[QueryBuilderInterface::class]);

        $post = new Post();

        $post->setQueryBuilder($this->services[QueryBuilderInterface::class]);

        echo $user->getQueryBuilder()->buildExecuteNoneQuery(), '<br>';

        print_r(
            $post->getQueryBuilder()->buildExecuteNoneQuery()
        );

    }
}