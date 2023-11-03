<?php

declare(strict_types = 1);

namespace App\Components\Tree\Sources\EntityParent;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EntityParentHelpers extends NObject
{


    /**
     * @param $entity IEntityParent|IEntityParent[]
     * @param $maxDepth int
     * @return IEntityParent|IEntityParent[]
     * @throws EntityParentHelpersException
     */
    public static function checkDepth($entity, int $maxDepth = 3)
    {
        //Recursive function
        function check(IEntityParent $entity, int $maxDepth, int $i = 0)
        {
            if ($i == $maxDepth) {
                throw new EntityParentHelpersException("Depth is greater than max '$maxDepth'.");
            } elseif ($parent = $entity->getParentEntity()) {
                check($parent, $maxDepth, ($i + 1));
            }
            return $entity;
        }

        //Check
        foreach (is_array($entity) ? $entity : [$entity] as $e) {
            if (!$e instanceof IEntityParent) {
                throw new EntityParentHelpersException("Object must be instance of '" . IEntityParent::class . "'. Type '" . gettype($e) . "' given.");
            }
            check($e, $maxDepth);
        }
        return $entity;
    }

}

class EntityParentHelpersException extends \Exception
{


}