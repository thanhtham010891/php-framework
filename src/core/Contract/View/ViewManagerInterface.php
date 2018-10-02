<?php

namespace System\Contract\View;

interface ViewManagerInterface
{

    /**
     * @return bool
     */
    public function getCacheStatus();

    /**
     * @return string $path
     */
    public function getCachePath();

    /**
     * @return string
     */
    public function getViewPath();
}