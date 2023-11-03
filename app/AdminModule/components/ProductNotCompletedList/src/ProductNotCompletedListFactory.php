<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductNotCompletedList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductNotCompletedListFactory
{


    /**
     * @return ProductNotCompletedList
     */
    public function create();
}