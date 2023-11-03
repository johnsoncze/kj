<?php

declare(strict_types = 1);

namespace App\Customer;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CustomerRepositoryFactory
{


    /**
     * @return CustomerRepository
     */
    public function create();
}