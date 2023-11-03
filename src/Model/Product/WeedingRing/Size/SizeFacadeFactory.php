<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Size;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SizeFacadeFactory
{


    /**
     * @return SizeFacade
     */
    public function create();
}