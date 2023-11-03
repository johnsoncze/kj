<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\VariantList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface VariantListFactory
{


    /**
     * @return VariantList
     */
    public function create();
}