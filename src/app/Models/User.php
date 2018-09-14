<?php

namespace App\Models;

use App\Core\Contract\Database\ConnectionInterface;

class User
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    private $password;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'status' => $this->getStatus(),
        ];
    }
}