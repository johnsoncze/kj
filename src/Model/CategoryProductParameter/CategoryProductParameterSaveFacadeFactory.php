<?php

namespace App\CategoryProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryProductParameterSaveFacadeFactory
{


    /**
     * @return CategoryProductParameterSaveFacade
     */
    public function create();
}