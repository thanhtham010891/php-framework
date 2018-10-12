<?php

namespace App\Providers\View\Response;

use System\Contract\View\Response\WebInterface;

use Twig_Environment;
use Twig_Loader_Filesystem;

class Web implements WebInterface
{
    private $options = [
        'viewPath' => false,
        'options' => [
            'cache' => false,
        ]
    ];

    public function setViewPath($viewPath)
    {
        $this->options['viewPath'] = $viewPath;
    }

    public function setCachePath($cachePath)
    {
        $this->options['cache'] = $cachePath;
    }

    /**
     * @param $view
     * @param array $args
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($view, array $args = [])
    {
        $twig = new Twig_Environment(

            new Twig_Loader_Filesystem($this->options['viewPath']), $this->options['options']

        );

        return $twig->render($view, $args);
    }
}