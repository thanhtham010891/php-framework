<?php

namespace App\Proviers\Services;

use App\Core\Contract\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session AS SymfonySession;

class Session implements SessionInterface
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @var SymfonySession
     */
    private $symfonySession;


    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function bootstrap()
    {
        $this->symfonySession = new SymfonySession();

        if (!$this->symfonySession->isStarted()) {
            $this->symfonySession->start();
        }
    }

    public function terminate()
    {
        // TODO: Implement terminate() method.
    }

    public function replicate()
    {
        return false;
    }

    public function all()
    {
        return $this->symfonySession->all();
    }

    public function set($key, $value)
    {
        $this->symfonySession->set($key, $value);
    }

    public function get($key, $value = '')
    {
        return $this->symfonySession->get($key, $value);
    }

    public function remove($key)
    {
        $this->symfonySession->remove($key);
    }

    public function clear()
    {
        $this->symfonySession->clear();
    }
}