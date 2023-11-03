<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroupParameter;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupParameterEntityFactory extends NObject
{


    /**
     * @param int $categoryFiltrationGroupId
     * @param int $productParameterId
     * @return CategoryFiltrationGroupParameterEntity
     */
    public function create(int $categoryFiltrationGroupId,
                           int $productParameterId)
    : CategoryFiltrationGroupParameterEntity
    {
        $entity = new CategoryFiltrationGroupParameterEntity();
        $entity->setCategoryFiltrationGroupId($categoryFiltrationGroupId);
        $entity->setProductParameterId($productParameterId);
        return $entity;
    }
}