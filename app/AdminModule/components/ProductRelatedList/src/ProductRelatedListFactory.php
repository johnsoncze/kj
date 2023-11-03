<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductRelatedList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductRelatedListFactory
{


    /**
     * @return ProductRelatedList
     */
    public function create();
}