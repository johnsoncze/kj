<?php

namespace App\ProductParameterGroup;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupSaveFacadeFactory
{


    /**
     * @return ProductParameterGroupSaveFacade
     */
    public function create();

}