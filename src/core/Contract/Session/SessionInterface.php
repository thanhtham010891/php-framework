<?php

namespace System\Contract\Session;

use System\Contract\ServiceInterface;

interface SessionInterface extends ServiceInterface
{

    /**
     * @return array
     */
    public function all();

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function get($key, $value = '');

    /**
     * @param string $key
     */
    public function remove($key);

    public function clear();
}