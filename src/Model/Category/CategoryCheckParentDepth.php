<?php

declare(strict_types = 1);

namespace App\Category;

use App\Components\Tree\Sources\EntityParent\EntityParentDepth;
use App\Components\Tree\Sources\EntityParent\EntityParentDepthException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryCheckParentDepth extends NObject
{


    /**
     * @param CategoryEntity $categoryEntity
     * @return CategoryEntity
     * @throws CategoryCheckParentDepthException
     */
    public function check(CategoryEntity $categoryEntity) : CategoryEntity
    {
        $max = CategoryEntity::MAX_PARENT_DEPTH;

        try {
            $entityParentDepth = new EntityParentDepth();
            $entityParentDepth->checkDepth($categoryEntity, $max);
            return $categoryEntity;
        } catch (EntityParentDepthException $exception) {
            throw new CategoryCheckParentDepthException(sprintf("Maximální počet zanoření kategorií jsou %s úrovně.", $max));
        }
    }
}