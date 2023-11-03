<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\PaymentList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PaymentListFactory
{


    /**
     * @return PaymentList
     */
    public function create();
}