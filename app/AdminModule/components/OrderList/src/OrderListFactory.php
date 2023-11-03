<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OrderList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OrderListFactory
{


    /**
     * @return OrderList
     */
    public function create();
}