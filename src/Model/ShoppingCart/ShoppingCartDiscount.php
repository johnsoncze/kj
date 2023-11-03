<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDiscount
{


    /** @var float */
    const BIRTHDAY_COUPON_DISCOUNT = 15;



    /**
     * @param ShoppingCart $shoppingCart
     * @param IShoppingCartPrice $price
     * @return IShoppingCartPrice
     * @throws \InvalidArgumentException
     */
    public function applyBirthdayCoupon(ShoppingCart $shoppingCart, IShoppingCartPrice $price) : IShoppingCartPrice
    {
        throw new \InvalidArgumentException('Do not use this method.');
    }
}