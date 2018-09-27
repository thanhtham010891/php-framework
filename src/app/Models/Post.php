<?php

namespace App\Models;

use App\Providers\Database\Model;

class Post extends Model
{
    protected $identity = [
        'table' => 'posts',
        'primaryKey' => 'id',
        'columns' => [
            'id', 'title'
        ]
    ];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}