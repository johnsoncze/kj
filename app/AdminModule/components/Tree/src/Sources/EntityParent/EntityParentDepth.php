<?php

declare(strict_types = 1);

namespace App\Components\Tree\Sources\EntityParent;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EntityParentDepth extends NObject
{


    /**
     * @param $entity IEntityParent|IEntityParent[]
     * @param $maxDepth int
     * @return IEntityParent|IEntityParent[]
     * @throws EntityParentDepthException
     */
    public function checkDepth($entity, int $maxDepth = 3)
    {
        //Check
        foreach (is_array($entity) ? $entity : [$entity] as $e) {
            if (!$e instanceof IEntityParent) {
                throw new EntityParentDepthException("Object must be instance of '" . IEntityParent::class . "'. Type '" . gettype($e) . "' given.");
            }
            $this->check($e, $maxDepth);
        }
        return $entity;
    }



    /**
     * @param IEntityParent $entity
     * @param int $maxDepth
     * @param int $i
     * @return IEntityParent
     * @throws EntityParentDepthException
     */
    protected function check(IEntityParent $entity, int $maxDepth, int $i = 0) : IEntityParent
    {
        if ($i == $maxDepth) {
            throw new EntityParentDepthException("Depth is greater than max '$maxDepth'.");
        } elseif ($parent = $entity->getParentEntity()) {
            $this->check($parent, $maxDepth, ($i + 1));
        }
        return $entity;
    }
}

class EntityParentDepthException extends \Exception
{


}