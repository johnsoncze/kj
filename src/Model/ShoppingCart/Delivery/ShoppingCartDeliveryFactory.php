<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Delivery;

use App\Delivery\Delivery;
use App\ShoppingCart\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDeliveryFactory
{


    /**
     * @param $shoppingCart ShoppingCart
     * @param $delivery Delivery
     * @return ShoppingCartDelivery
     */
    public function create(ShoppingCart $shoppingCart,
                           Delivery $delivery) : ShoppingCartDelivery
    {
        $cartDelivery = new ShoppingCartDelivery();
        $cartDelivery->setShoppingCartId($shoppingCart->getId());
        $cartDelivery->setDeliveryId($delivery->getId());
        $cartDelivery->setName($delivery->getTranslation()->getName());
        $cartDelivery->setDiscount(0.0);
        $cartDelivery->setPrice($delivery->getPrice());
        $cartDelivery->setVat($delivery->getVat());

        return $cartDelivery;
    }
}