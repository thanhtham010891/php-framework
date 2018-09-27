<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Providers\Database\Model\UseModelTrait;

use System\Controller;

class IndexController extends Controller
{

    use UseModelTrait;

    /**
     * @throws \System\BaseException
     */
    public function index()
    {
        /**
         * @var User $user
         * @var Post $post
         */
        $user = $this->getModel(User::class);
        $post = $this->getModel(Post::class);

        $builder = $user->getQueryBuilder();

        $builder->whereIn('id', [1, 2, 3])->whereEqual('email', '"thamtt@nal.vn"');
        $builder->innerJoin('posts', $post->getQueryBuilder()->whereEqual('user_id', 'users.id'));

        echo $builder->buildExecuteNoneQuery();
    }
}