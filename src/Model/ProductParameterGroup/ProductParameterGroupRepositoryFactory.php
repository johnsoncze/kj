<?php

namespace App\ProductParameterGroup;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupRepositoryFactory
{


    /**
     * @return ProductParameterGroupRepository
     */
    public function create();
}