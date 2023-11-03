<?php

namespace App\CategoryProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryProductParameterRepositoryFactory
{


    /**
     * @return CategoryProductParameterRepository
     */
    public function create();
}