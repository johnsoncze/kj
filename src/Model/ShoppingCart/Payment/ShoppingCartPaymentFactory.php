<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Payment;

use App\Payment\Payment;
use App\ShoppingCart\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartPaymentFactory
{


    /**
     * @param $shoppingCart ShoppingCart
     * @param $payment Payment
     * @return ShoppingCartPayment
     */
    public function create(ShoppingCart $shoppingCart,
                           Payment $payment) : ShoppingCartPayment
    {
        $cartPayment = new ShoppingCartPayment();
        $cartPayment->setName($payment->getTranslation()->getName());
        $cartPayment->setShoppingCartId($shoppingCart->getId());
        $cartPayment->setPaymentId($payment->getId());
        $cartPayment->setDiscount(0.0);
        $cartPayment->setPrice($payment->getPrice());
        $cartPayment->setVat($payment->getVat());

        return $cartPayment;
    }
}