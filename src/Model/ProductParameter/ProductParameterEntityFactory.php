<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterEntityFactory extends NObject
{


    /**
     * @param int $productParameterGroupId
     * @return ProductParameterEntity
     */
    public function create(int $productParameterGroupId) : ProductParameterEntity
    {
        $entity = new ProductParameterEntity();
        $entity->setProductParameterGroupId($productParameterGroupId);

        return $entity;
    }
}