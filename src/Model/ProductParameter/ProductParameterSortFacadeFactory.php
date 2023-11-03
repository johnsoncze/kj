<?php

namespace App\ProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterSortFacadeFactory
{


    /**
     * @return ProductParameterSortFacade
     */
    public function create();
}