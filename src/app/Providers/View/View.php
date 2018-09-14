<?php

namespace App\Providers\View;

use App\Core\Contract\ResponseInterface;
use App\Core\Contract\ViewInterface;
use App\Exceptions\ApplicationException;

class View implements ViewInterface
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @var array
     */
    private $resource;

    /**
     * @var ResponseInterface $response
     */
    private $response;

    /**
     * @var string
     */
    private $viewPath;

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    public function setViewPath($path)
    {
        $this->viewPath = $path;
    }

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function bootstrap()
    {
    }

    public function terminate()
    {
        if ($this->getResponse()) {
            $this->getResponse()->send();
        }
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @throws ApplicationException
     */
    public function run()
    {
        $viewResource = $this->resource;

        if ($viewResource instanceof ResponseInterface) {
            return true;
        }

        if (empty($viewResource) || empty($viewResource['type'])) {

            $this->getResponse()->prepare($this->_render(
                $this->settings['views']['page_404'], []
            ), 404);

            return true;
        }

        if (empty($viewResource['data'])) {
            $viewResource['data'] = [];
        }

        $type = $viewResource['type'];

        if ($type === 'exit') {

            exit(200);

        } elseif ($type === 'html') {

            if (empty($viewResource['path'])) {
                throw new ApplicationException('Path of view is not registered');
            }

            $this->getResponse()->prepare($this->_render(
                $viewResource['path'], $viewResource['data']
            ), 200);

        } elseif ($type === 'json') {

            if (empty($viewResource['status'])) {
                throw new ApplicationException('Status of json is not registered');
            }

            $this->getResponse()->prepare(
                json_encode($viewResource['data']), $viewResource['status'], ['Content-type' => 'application/json']
            );

        }

        return true;
    }

    /**
     * @param $path
     * @param array $params
     * @return string
     * @throws ApplicationException
     */
    private function _render($path, $params = [])
    {
        $path = $this->getViewPath() . trim($path);

        if (!file_exists($path)) {
            throw new ApplicationException('Path of Views "' . $path . '" is not registered!');
        }

        extract($params);

        ob_start();

        require_once $path;;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }
}