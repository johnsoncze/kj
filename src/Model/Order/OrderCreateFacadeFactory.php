<?php

declare(strict_types = 1);

namespace App\Order;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OrderCreateFacadeFactory
{


    /**
     * @return OrderCreateFacade
     */
    public function create();
}