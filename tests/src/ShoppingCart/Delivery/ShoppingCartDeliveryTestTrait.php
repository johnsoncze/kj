<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Delivery;

use App\ShoppingCart\Delivery\ShoppingCartDelivery;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ShoppingCartDeliveryTestTrait
{


    /**
     * @return ShoppingCartDelivery
     */
    public function createTestShoppingCartDelivery() : ShoppingCartDelivery
    {
        $delivery = new ShoppingCartDelivery();
        $delivery->setShoppingCartId(1);
        $delivery->setDeliveryId(1);
        $delivery->setName('Delivery');
        $delivery->setPrice(150.50);
        $delivery->setVat(21.0);
        $delivery->setDiscount(0.0);

        return $delivery;
    }
}