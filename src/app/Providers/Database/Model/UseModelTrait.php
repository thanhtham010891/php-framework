<?php

namespace App\Providers\Database\Model;

use System\Contract\Database\QueryBuilderInterface;
use System\Contract\Database\DatabaseInterface;
use System\Contract\Database\ModelInterface;
use System\BaseException;

trait UseModelTrait
{

    /**
     * @var DatabaseInterface $database
     */
    private $database;

    /**
     * @param $model
     * @return ModelInterface
     * @throws BaseException
     */
    public function getModel($model)
    {
        $model = new $model;

        if ($model instanceof ModelInterface) {

            $model->bootstrap(
                $this->services[DatabaseInterface::class], $this->services[QueryBuilderInterface::class]
            );

            return $model;
        }

        throw new BaseException('Database > Model: "' . $model . '" should be implements from ' . ModelInterface::class);
    }
}