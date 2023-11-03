<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterSetList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterSetListFactory
{


    /**
     * @return ProductParameterSetList
     */
    public function create() : ProductParameterSetList;
}