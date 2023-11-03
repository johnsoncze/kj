<?php

declare(strict_types = 1);

namespace App\Customer;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CustomerSignFacadeFactory
{


    /**
     * @return CustomerSignFacade
     */
    public function create();
}