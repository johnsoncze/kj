<?php

declare(strict_types = 1);

namespace App\Tests\Order;

use App\Order\Order;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait OrderTestTrait
{


    /**
     * @return Order
     */
    private function createTestOrder() : Order
    {
        $order = new Order();
        $order->setCode('ABC123456789');
        $order->setDeliveryPrice(50);
        $order->setDeliveryVat(21.0);
        $order->setPaymentPrice(150);
        $order->setDeliveryVat(21.0);
        $order->setSummaryPriceWithoutVat(5250.0);
        $order->setSummaryPrice(5500.0);

        return $order;
    }
}