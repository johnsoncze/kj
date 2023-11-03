<?php

namespace App\ProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterSaveFacadeFactory
{


    /**
     * @return ProductParameterSaveFacade
     */
    public function create();
}