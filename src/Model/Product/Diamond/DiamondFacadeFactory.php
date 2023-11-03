<?php

declare(strict_types = 1);

namespace App\Product\Diamond;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DiamondFacadeFactory
{


    /**
     * @return DiamondFacade
     */
    public function create();
}