<?php

namespace App\Components\ProductParameterList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterListFactory
{


    /**
     * @return ProductParameterList
     */
    public function create();
}