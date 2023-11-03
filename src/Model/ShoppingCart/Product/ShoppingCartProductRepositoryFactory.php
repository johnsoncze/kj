<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShoppingCartProductRepositoryFactory
{


    /**
     * @return ShoppingCartProductRepository
     */
    public function create();
}