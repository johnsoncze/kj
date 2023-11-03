<?php

declare(strict_types = 1);

namespace App\Tests\Payment;

use App\Payment\Payment;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait PaymentTestTrait
{


    /**
     * @return Payment
     */
    private function createTestPayment() : Payment
    {
        $payment = new Payment();
        $payment->setPrice(100.50);
        $payment->setVat(21.0);
        $payment->setExternalSystemId(123);
        $payment->setCreditCard(TRUE);
        $payment->setTransfer(FALSE);
        $payment->setState(Payment::ALLOWED);

        return $payment;
    }
}