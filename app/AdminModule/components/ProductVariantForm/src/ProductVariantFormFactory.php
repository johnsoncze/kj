<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductVariantForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductVariantFormFactory
{


    /**
     * @return ProductVariantForm
     */
    public function create() : ProductVariantForm;
}