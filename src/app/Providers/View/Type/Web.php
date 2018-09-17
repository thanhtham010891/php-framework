<?php

namespace App\Providers\View\Type;

use App\Core\Contract\Controllers\WebInterface;
use App\Core\Contract\ViewManagerInterface;
use App\Core\Controller;
use App\Exceptions\ApplicationException;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Web extends Controller implements WebInterface
{

    /**
     * @var Twig_Environment;
     */
    private $twig;

    /**
     * @param array $args
     * @throws ApplicationException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(array $args)
    {
        if (empty($args['render'])) {
            throw new ApplicationException('View path is not registered');
        }

        if (empty($args['data'])) {
            $args['data'] = [];
        }

        echo $this->getTwig()->render(
            $args['render'], $args['data']
        );
    }

    public function getTwig()
    {
        if (empty($this->twig)) {

            /**
             * @var ViewManagerInterface $viewManager
             */
            $viewManager = $this->services[ViewManagerInterface::class];

            $options = [];

            if ($viewManager->getCacheStatus()) {
                $options['cache'] = $viewManager->getCachePath();
            }

            $this->twig = new Twig_Environment(
                new Twig_Loader_Filesystem($viewManager->getViewPath()), $options
            );
        }

        return $this->twig;
    }

}