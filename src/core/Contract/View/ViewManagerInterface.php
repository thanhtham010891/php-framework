<?php

namespace System\Contract\View;

use System\Contract\View\Response\WebInterface;
use System\Contract\View\Response\ApiInterface;

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

    /**
     * @param WebInterface $web
     * @return void
     */
    public function setWeb(WebInterface $web);

    /**
     * @param ApiInterface $api
     * @return void
     */
    public function setApi(ApiInterface $api);

    /**
     * @param $viewPath
     * @param array $args
     * @return mixed
     */
    public function render($viewPath, array $args = []);
}