<?php

namespace System\Contract\Http;

interface RouteInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRequire();

    /**
     * @return mixed
     */
    public function getController();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param array
     */
    public function getParams();
}