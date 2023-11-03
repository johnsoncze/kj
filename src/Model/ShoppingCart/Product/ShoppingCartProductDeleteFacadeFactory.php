<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShoppingCartProductDeleteFacadeFactory
{


    /**
     * @return ShoppingCartProductDeleteFacade
     */
    public function create();
}