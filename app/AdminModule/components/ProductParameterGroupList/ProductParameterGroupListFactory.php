<?php

namespace App\Components\ProductParameterGroupList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupListFactory
{


    /**
     * @return ProductParameterGroupList
     */
    public function create();
}