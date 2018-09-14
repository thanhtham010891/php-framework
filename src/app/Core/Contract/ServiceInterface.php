<?php

namespace App\Core\Contract;

interface ServiceInterface
{
    public function bootstrap();

    public function terminate();
}