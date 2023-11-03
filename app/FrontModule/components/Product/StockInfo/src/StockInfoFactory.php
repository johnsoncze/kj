<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\StockInfo;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface StockInfoFactory
{


    /**
     * @return StockInfo
     */
    public function create();
}