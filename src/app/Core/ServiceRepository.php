<?php

namespace App\Core;

use App\Core\Contract\ServiceInterface;
use App\Exceptions\ApplicationException;


class ServiceRepository implements \ArrayAccess
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $settings = [];

    /**
     * Service is booted once time
     *
     * @var array
     */
    private $alreadyBooted = [];

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function settings($settings)
    {
        $this->settings = array_replace($this->settings, $settings);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->services;
    }

    /**
     * @param mixed $contract
     * @return mixed
     * @throws ApplicationException
     */
    public function offsetGet($contract)
    {
        if (!$this->offsetExists($contract)) {
            throw new ApplicationException('Services "' . $contract . '" is not registered');
        }

        /**
         * @var ServiceInterface $service
         */
        $service = $this->services[$contract];

        if ($service instanceof ServiceInterface && !in_array($contract, $this->alreadyBooted)) {
            $this->alreadyBooted[] = $contract;
            $service->bootstrap();
        }

        return $service;
    }

    /**
     * @param mixed $contract
     * @param mixed $service
     * @throws ApplicationException
     */
    public function offsetSet($contract, $service)
    {
        if (!interface_exists($contract)) {
            throw new ApplicationException('Interface "' . $contract . '" does not exist');
        }

        if (is_callable($service)) {
            $service = $service($this->settings);
        }

        if ($service instanceof $contract) {
            $this->services[$contract] = $service;
        } else {
            throw new ApplicationException(
                'Services "' . get_class($service) . '" must be implemented by ' . $this->services[$contract]
            );
        }
    }

    /**
     * @param mixed $contract
     * @return bool
     */
    public function offsetExists($contract)
    {
        return empty($this->services[$contract]) ? false : true;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->services[$offset]);
    }
}