<?php

use System\Application;
use System\BaseException;
use System\Contract\View\ViewManagerInterface;

/**
 * @param string $more
 * @return string
 */
function base_path($more = '')
{
    $settings = Application::instance()->getSettings();

    return rtrim($settings['base_dir'], '/') . '/' . (empty($more) ? '' : trim($more) . '/');
}

/**
 * @return string
 */
function app_path()
{
    $settings = Application::instance()->getSettings();

    return base_path($settings['app']['path']);
}

/**
 * @return string
 */
function config_path()
{
    $settings = Application::instance()->getSettings();

    return base_path($settings['config']['path']);
}

/**
 * @return string
 */
function storage_path()
{
    $settings = Application::instance()->getSettings();

    return base_path($settings['storage']['path']);
}

/**
 * @return string
 */
function provider_path()
{
    $settings = Application::instance()->getSettings();

    return base_path($settings['providers']['path']);
}

/**
 * @return string
 */
function public_path()
{
    $settings = Application::instance()->getSettings();

    return base_path($settings['public']['path']);
}

/**
 * You must implement ViewManagerInterface to get path
 *
 * @return string
 */
function view_path()
{
    /**
     * @var ViewManagerInterface $view
     */
    $view = Application::instance()->getService(ViewManagerInterface::class);

    return $view->getViewPath();
}

/**
 * Safe require file
 *
 * @param $file
 * @return mixed
 * @throws BaseException
 */
function require_path($file)
{
    if ($file === Application::instance()->getMainFile()) {
        throw new BaseException('You are requiring main file ' . $file);
    }

    if (!file_exists($file)) {
        throw new BaseException('File "' . $file . '" does not exist');
    }

    return require_once($file);
}