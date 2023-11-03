<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Payment;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShoppingCartPaymentSaveFacadeFactory
{


    /**
     * @return ShoppingCartPaymentSaveFacade
     */
    public function create();
}