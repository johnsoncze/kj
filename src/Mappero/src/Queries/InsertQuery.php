<?php

namespace Ricaefeliz\Mappero\Queries;

use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class InsertQuery extends BaseQuery implements IQuery
{


    /**
     * @param $entity IEntity|IEntity[]
     * @return int|void
     */
    public function execute($entity)
    {
        if (is_array($entity)) {
            return $this->insertMore($entity);
        }
        $data = Entities::getArrayForSave($entity);
        return $this->database->insert($entity::getAnnotation()->getTable()->getName(), $data);
    }



    /**
     * @param $entities IEntity[]
     * @return int|void
     */
    protected function insertMore(array $entities)
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = Entities::getArrayForSave($entity);
        }
        return $this->database->insert($entities[0]::getAnnotation()->getTable()->getName(), $data);
    }
}