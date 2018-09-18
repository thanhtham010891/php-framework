<?php

namespace App\Providers\View;

use App\Core\Contract\ViewManagerInterface;

class Manager implements ViewManagerInterface
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @return string
     */
    public function getViewPath()
    {
        return rtrim($this->settings['path'], '/') . '/';
    }

    public function getCacheStatus()
    {
        return $this->settings['cache']['status'];
    }

    public function getCachePath()
    {
        return rtrim($this->settings['cache']['path'], '/');
    }

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

}