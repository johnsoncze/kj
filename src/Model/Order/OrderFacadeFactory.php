<?php

declare(strict_types = 1);

namespace App\Order;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OrderFacadeFactory
{


    /**
     * @return OrderFacade
     */
    public function create();
}