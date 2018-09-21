<?php

namespace App\Providers\View\Type;

use App\Core\Contract\ViewManagerInterface;
use App\Exceptions\ApplicationException;
use Twig_Environment;
use Twig_Loader_Filesystem;

trait WebTrait
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

        echo $this->_getTwig()->render(
            $args['render'], $args['data']
        );
    }

    private function _getTwig()
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