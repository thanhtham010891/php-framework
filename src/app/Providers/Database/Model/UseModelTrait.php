<?php

namespace App\Providers\Database\Model;

use App\Core\Contract\Database\QueryBuilderInterface;
use App\Core\Contract\Database\DatabaseInterface;
use App\Core\Contract\Database\ModelInterface;
use App\Exceptions\ApplicationException;

trait UseModelTrait
{

    /**
     * @var DatabaseInterface $database
     */
    private $database;

    /**
     * @param $model
     * @return ModelInterface
     * @throws ApplicationException
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

        throw new ApplicationException('Database > Model: "' . $model . '" should be implements from ' . ModelInterface::class);
    }
}