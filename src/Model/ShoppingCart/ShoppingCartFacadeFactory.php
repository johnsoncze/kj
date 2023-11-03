<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShoppingCartFacadeFactory
{


    /**
     * @return ShoppingCartFacade
     */
    public function create();
}