<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductStateList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductStateListFactory
{


    /**
     * @return ProductStateList
     */
    public function create();
}