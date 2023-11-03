<?php

declare(strict_types = 1);

namespace App\Product\Variant;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface VariantStorageFacadeFactory
{


    /**
     * @return VariantStorageFacade
     */
    public function create();
}