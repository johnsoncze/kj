<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\PriceInfo;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PriceInfoFactory
{


    /**
     * @return PriceInfo
     */
    public function create();
}