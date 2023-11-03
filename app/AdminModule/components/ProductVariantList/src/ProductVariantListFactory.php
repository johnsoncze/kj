<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductVariantList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductVariantListFactory
{


    /**
     * @return ProductVariantList
     */
    public function create() : ProductVariantList;
}