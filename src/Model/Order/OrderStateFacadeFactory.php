<?php

declare(strict_types = 1);

namespace App\Order;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OrderStateFacadeFactory
{


    /**
     * @return OrderStateFacade
     */
    public function create();
}