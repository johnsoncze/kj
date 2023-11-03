<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSetSort extends NObject
{


    /**
     * @param ProductParameterEntity $productParameterEntity
     * @param int $sort
     * @return ProductParameterEntity
     */
    public function set(ProductParameterEntity $productParameterEntity, int $sort)
    : ProductParameterEntity
    {
        $productParameterEntity->setSort($sort);
        return $productParameterEntity;
    }
}