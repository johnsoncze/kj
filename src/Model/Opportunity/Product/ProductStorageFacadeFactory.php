<?php

declare(strict_types = 1);

namespace App\Opportunity\Product;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductStorageFacadeFactory
{


    /**
     * @return ProductStorageFacade
     */
    public function create();
}