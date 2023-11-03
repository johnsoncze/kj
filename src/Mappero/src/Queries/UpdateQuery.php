<?php

namespace Ricaefeliz\Mappero\Queries;

use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UpdateQuery extends BaseQuery implements IQuery
{


    /**
     * @param $entity IEntity
     * @return mixed
     */
    public function execute(IEntity $entity)
    {
        $data = Entities::getArrayForSave($entity);
        $annotation = $entity::getAnnotation();
        $table = $annotation->getTable()->getName();
        $primaryColumn = $annotation->getPrimaryProperty()->getColumn();
        return $this->database->update($table, $primaryColumn->getName(), $entity->getId(), $data);
    }

}