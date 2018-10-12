<?php

namespace System\Contract\View\Response;

interface WebInterface
{
    public function render($viewPath, array $args = []);

    public function setCachePath($cachePath);

    public function setViewPath($viewPath);
}