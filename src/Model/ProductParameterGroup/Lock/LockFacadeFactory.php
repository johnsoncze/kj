<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Lock;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface LockFacadeFactory
{


    /**
     * @return LockFacade
     */
    public function create();
}