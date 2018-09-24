<?php

namespace App\Models;

use App\Providers\Database\Model;

class User extends Model
{
    protected $identity = [
        'table' => 'users',
        'primaryKey' => 'id',
        'columns' => [
            'id', 'email', 'status'
        ]
    ];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

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
    public function getEmail()
    {
        return $this->email;
    }
}