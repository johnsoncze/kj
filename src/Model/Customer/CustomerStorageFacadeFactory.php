<?php

declare(strict_types = 1);

namespace App\Customer;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CustomerStorageFacadeFactory
{


    /**
     * @return CustomerStorageFacade
     */
    public function create();
}