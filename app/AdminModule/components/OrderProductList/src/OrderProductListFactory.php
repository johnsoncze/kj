<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OrderProductList;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OrderProductListFactory
{


    /**
     * @return OrderProductList
     */
    public function create();
}