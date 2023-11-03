<?php

namespace App\ProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterRemoveFacadeFactory
{


    /**
     * @return ProductParameterRemoveFacade
     */
    public function create();
}