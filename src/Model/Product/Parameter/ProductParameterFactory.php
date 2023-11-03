<?php

declare(strict_types = 1);

namespace App\Product\Parameter;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterFactory extends NObject
{


    /**
     * @param int $productId
     * @param int $parameterId
     * @return ProductParameter
     */
    public function create(int $productId, int $parameterId) : ProductParameter
    {
        $parameter = new ProductParameter();
        $parameter->setProductId($productId);
        $parameter->setParameterId($parameterId);
        $parameter->setAddDate(new \DateTime());

        return $parameter;
    }
}