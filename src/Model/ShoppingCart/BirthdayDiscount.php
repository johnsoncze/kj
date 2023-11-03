<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\ShoppingCart\Product\Discount;
use App\ShoppingCart\Product\ShoppingCartProductRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class BirthdayDiscount
{


    /** @var ShoppingCartProductRepository */
    protected $cartProductRepo;

    /** @var ShoppingCartRepository */
    protected $cartRepo;

    /** @var Discount */
    protected $discount;



    public function __construct(Discount $discount,
                                ShoppingCartProductRepository $cartProductRepository,
                                ShoppingCartRepository $cartRepo)
    {
        $this->cartProductRepo = $cartProductRepository;
        $this->cartRepo = $cartRepo;
        $this->discount = $discount;
    }



    /**
     * Apply discount on shopping cart.
     * @param $cart ShoppingCart
     * @return ShoppingCart
     */
    public function apply(ShoppingCart $cart) : ShoppingCart
    {
        $cart->setBirthdayCoupon(TRUE);
        $this->cartRepo->save($cart);
        $this->processBirthdayCouponOnProducts($cart);
        return $cart;
    }



    /**
     * Remove birthday discount from shopping cart.
     * @param $cart ShoppingCart
     * @return ShoppingCart
     */
    public function remove(ShoppingCart $cart) : ShoppingCart
    {
        $cart->setBirthdayCoupon(FALSE);
        $this->cartRepo->save($cart);
        $this->processBirthdayCouponOnProducts($cart);
        return $cart;
    }



    /**
     * Process birthday coupon on products of shopping cart
     * @param $cart ShoppingCart
     * @return ShoppingCart
     */
    private function processBirthdayCouponOnProducts(ShoppingCart $cart) : ShoppingCart
    {
        $cartProducts = $this->cartProductRepo->findByCartId($cart->getId());
        foreach ($cartProducts as $cartProduct) {
            $this->discount->applyDiscount($cart, $cartProduct);
            $this->cartProductRepo->save($cartProduct);
        }
        return $cart;
    }
}