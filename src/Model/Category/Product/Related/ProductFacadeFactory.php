<?php

declare(strict_types = 1);

namespace App\Category\Product\Related;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductFacadeFactory
{


    /**
     * @return ProductFacade
     */
    public function create();
}