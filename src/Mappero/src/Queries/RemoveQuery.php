<?php

namespace Ricaefeliz\Mappero\Queries;

use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RemoveQuery extends BaseQuery implements IQuery
{


    /**
     * @param $entity IEntity|IEntity[]
     * @return int
     */
    public function execute($entity)
    {
        $isArray = is_array($entity);
        $id = $isArray ? Entities::getId($entity) : $entity->getId();
        $annotation = $isArray ? end($entity)::getAnnotation() : $entity::getAnnotation();
        $table = $annotation->getTable()->getName();
        $primaryColumn = $annotation->getPrimaryProperty()->getColumn();
        return $this->database->remove($table, $primaryColumn->getName(), $id);
    }
}