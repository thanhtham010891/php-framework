<?php

namespace System\Contract\Http;


interface RequestInterface
{

    /**
     * @return bool
     */
    public function isRequestPost();

    /**
     * @return bool
     */
    public function isRequestGet();

    /**
     * http://test.local/hello?world=123 -> hello
     * @return string
     */
    public function getPath();

    /**
     * http://test.local/hello?world=123 -> world=123
     * @return string
     */
    public function getQueryString();

    /**
     * http://test.local/hello?world=123 -> ['world' => 123]
     * @return array
     */
    public function getAllQueryParam();

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getParam($key, $default = '');

    /**
     * @return string
     */
    public function toJson();
}