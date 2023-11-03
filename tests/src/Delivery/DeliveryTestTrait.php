<?php

declare(strict_types = 1);

namespace App\Tests\Delivery;

use App\Delivery\Delivery;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait DeliveryTestTrait
{


    /**
     * @return Delivery
     */
    private function createTestDelivery() : Delivery
    {
        $delivery = new Delivery();
        $delivery->setExternalSystemId(5);
        $delivery->setPrice(150.50);
        $delivery->setVat(21.0);
        $delivery->setState(Delivery::ALLOWED);

        return $delivery;
    }
}