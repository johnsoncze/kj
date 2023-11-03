<?php

declare(strict_types = 1);

namespace App\Order\Product\Parameter;

use App\Order\Product\Product;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameterGroup\ProductParameterGroupEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ParameterFactory
{


    /**
     * @param $orderProduct Product
     * @param $group ProductParameterGroupEntity
     * @param $parameter ProductParameterEntity
     * @return Parameter
     */
    public function create(Product $orderProduct,
                           ProductParameterGroupEntity $group,
                           ProductParameterEntity $parameter) : Parameter
    {
        $_parameter = new Parameter();
        $_parameter->setProductId($orderProduct->getId());
        $_parameter->setParameterGroupId($group->getId());
        $_parameter->setName($group->getTranslation()->getName());
        $_parameter->setParameterId($parameter->getId());
        $_parameter->setValue($parameter->getTranslation()->getValue());
        return $_parameter;
    }
}