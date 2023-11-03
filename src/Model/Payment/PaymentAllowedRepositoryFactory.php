<?php

declare(strict_types = 1);

namespace App\Payment;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PaymentAllowedRepositoryFactory
{


    /**
     * @return PaymentAllowedRepository
     */
    public function create();
}