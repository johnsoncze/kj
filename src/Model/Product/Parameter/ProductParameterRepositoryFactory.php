<?php

declare(strict_types = 1);

namespace App\Product\Parameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterRepositoryFactory
{


    /**
     * @return ProductParameterRepository
     */
    public function create();
}