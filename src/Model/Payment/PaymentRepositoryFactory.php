<?php

declare(strict_types = 1);

namespace App\Payment;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PaymentRepositoryFactory
{


    /**
     * @return PaymentRepository
     */
    public function create();
}