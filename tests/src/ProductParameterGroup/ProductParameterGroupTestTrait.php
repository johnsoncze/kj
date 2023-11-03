<?php

declare(strict_types = 1);

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductParameterGroupTestTrait
{


    /**
     * @return ProductParameterGroupEntity
     */
    private function createTestProductParameterGroup()
    {
        $group = new ProductParameterGroupEntity();
        $group->setFiltrationType(ProductParameterGroupEntity::FILTRATION_TYPE_LIST);
        $group->setVariantType(ProductParameterGroupEntity::VARIANT_TYPE_SELECTBOX);
        $group->setVisibleInOrder(FALSE);

        return $group;
    }
}