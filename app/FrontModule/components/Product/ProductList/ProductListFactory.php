<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ProductList;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductListFactory
{


    /**
     * @return ProductList
     */
    public function create();
}