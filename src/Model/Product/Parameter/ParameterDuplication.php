<?php

declare(strict_types = 1);

namespace App\Product\Parameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ParameterDuplication
{


    /**
     * Check if exists the same parameter of product.
     * @param $parameter ProductParameter
     * @param $productParameterRepo ProductParameterRepository
     * @return ProductParameter
     * @throws ParameterDuplicationException
     */
    public function check(ProductParameter $parameter, ProductParameterRepository $productParameterRepo) : ProductParameter
    {
        $duplicateParameter = $productParameterRepo->findOneByProductIdAndParameterId($parameter->getProductId(), $parameter->getParameterId());
        if ($duplicateParameter) {
            throw new ParameterDuplicationException('Parametr již existuje.');
        }
        return $parameter;
    }
}