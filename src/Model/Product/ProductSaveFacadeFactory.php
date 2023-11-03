<?php

declare(strict_types = 1);

namespace App\Product;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductSaveFacadeFactory
{


    /**
     * @return ProductSaveFacade
     */
    public function create();
}