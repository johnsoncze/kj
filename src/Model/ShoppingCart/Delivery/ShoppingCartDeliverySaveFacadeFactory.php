<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Delivery;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShoppingCartDeliverySaveFacadeFactory
{


    /**
     * @return ShoppingCartDeliverySaveFacade
     */
    public function create();
}