<?php

namespace System;

use System\Contract\ServiceInterface;

class ServiceRepository implements \ArrayAccess
{
    /**
     * All services inject to application is singleton.
     * If you would like to ServiceInterface::replicate it then set it is true
     *
     * @var array
     */
    private $services = [];

    /**
     * Service snapshot
     *
     * @var array
     */
    private $snapshot = [];

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
     * @throws BaseException
     */
    public function offsetGet($contract)
    {
        if (!$this->offsetExists($contract)) {
            throw new BaseException('Services "' . $contract . '" is not registered');
        }

        if (!empty($this->snapshot[$contract])) {
            $this->offsetSet($contract, $this->snapshot[$contract]);
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
     * @throws BaseException
     */
    public function offsetSet($contract, $service)
    {
        if (!interface_exists($contract)) {
            throw new BaseException('Interface "' . $contract . '" does not exist');
        }

        if (is_callable($service)) {
            $object = $service($this->settings);
        } else {
            $object = $service;
        }

        if ($object instanceof $contract) {

            $this->services[$contract] = $object;

            if (
                $object instanceof ServiceInterface && $object->replicate()
            ) {
                if ($object->replicate() && empty($this->snapshot[$contract])) {
                    $this->snapshot[$contract] = $service;
                } elseif (!$object->replicate() && !empty($this->snapshot[$contract])) {
                    unset($this->snapshot[$contract]);
                }
            }

        } else {
            throw new BaseException(
                'Services "' . get_class($object) . '" must be implemented by ' . $this->services[$contract]
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