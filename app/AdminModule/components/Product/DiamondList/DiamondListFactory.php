<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Product\DiamondList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DiamondListFactory
{


    /**
     * @return DiamondList
     */
    public function create();
}