<?php

namespace App\Controllers;

use App\Models\User;
use App\Providers\Database\Model;
use System\Contract\Database\DatabaseInterface;
use System\Contract\Database\QueryBuilderInterface;
use System\Contract\View\ViewManagerInterface;
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

        echo '<pre>';

        print_r(
            $user->findById(1)
        );

//        $post = new Post();
//
//        $post->setQueryBuilder($this->services[QueryBuilderInterface::class]);
//
//        echo $user->getQueryBuilder()->buildExecuteNoneQuery(), '<br>';

    }

    public function render()
    {
        /**
         * @var ViewManagerInterface $view
         */
        $view = $this->services[ViewManagerInterface::class];

        return $view->render('index.html');
    }
}