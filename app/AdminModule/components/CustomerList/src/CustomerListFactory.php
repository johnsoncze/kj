<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CustomerList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CustomerListFactory
{


    /**
     * @return CustomerList
     */
    public function create();
}