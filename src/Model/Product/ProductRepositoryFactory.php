<?php

declare(strict_types = 1);

namespace App\Product;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductRepositoryFactory
{


    /**
     * @return ProductRepository
     */
    public function create();
}