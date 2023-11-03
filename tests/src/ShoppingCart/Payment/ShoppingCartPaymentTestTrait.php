<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Payment;

use App\ShoppingCart\Payment\ShoppingCartPayment;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ShoppingCartPaymentTestTrait
{


    /**
     * @return ShoppingCartPayment
     */
    private function createTestShoppingCartPayment() : ShoppingCartPayment
    {
        $payment = new ShoppingCartPayment();
        $payment->setShoppingCartId(1);
        $payment->setName('Payment');
        $payment->setPaymentId(1);
        $payment->setPrice(150.50);
        $payment->setVat(21.0);
        $payment->setDiscount(0.0);

        return $payment;
    }
}