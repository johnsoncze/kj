<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\MetaSmallBlock;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface MetaSmallBlockFactory
{


    /**
     * @return MetaSmallBlock
     */
    public function create();
}