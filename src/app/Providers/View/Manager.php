<?php

namespace App\Providers\View;

use System\Contract\View\Response\ApiInterface;
use System\Contract\View\Response\WebInterface;
use System\Contract\View\ViewManagerInterface;

class Manager implements ViewManagerInterface
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @var ApiInterface
     */
    private $api;

    /**
     * @var WebInterface
     */
    private $web;

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

    public function setApi(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function setWeb(WebInterface $web)
    {
        $this->web = $web;
    }

    public function render($viewPath, array $args = [])
    {
        $this->web->setViewPath($this->getViewPath());

        if ($this->getCacheStatus()) {
            $this->web->setCachePath($this->getCachePath());
        }

        echo $this->web->render($viewPath, $args);
    }

}