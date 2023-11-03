<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Diamond\PriceList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PriceListFactory
{


    /**
     * @return PriceList
     */
    public function create();
}