<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\OrderBlock;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OrderBlockFactory
{


    /**
     * @return OrderBlock
     */
    public function create();
}