<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterGroupLockList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupLockListFactory
{


    /**
     * @return ProductParameterGroupLockList
     */
    public function create();
}