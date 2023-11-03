<?php

namespace App\ProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterFindFacadeFactory
{


    /**
     * @return ProductParameterFindFacade
     */
    public function create();
}