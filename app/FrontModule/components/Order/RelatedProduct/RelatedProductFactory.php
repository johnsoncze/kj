<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Order\RelatedProduct;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface RelatedProductFactory
{


    /**
     * @return RelatedProduct
     */
    public function create();
}