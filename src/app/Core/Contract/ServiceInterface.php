<?php

namespace App\Core\Contract;

interface ServiceInterface
{

    /**
     * @return void
     */
    public function bootstrap();

    /**
     * @return void
     */
    public function terminate();

    /**
     * All services inject to application is singleton. If you would like to replicate it then set it is true
     *
     * @return bool
     */
    public function replicate();
}