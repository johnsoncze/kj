<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use App\ShoppingCart\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Merger
{


    /** @var Discount */
    protected $discount;



    public function __construct(Discount $discount)
    {
        $this->discount = $discount;
    }



    /**
     * Move products to cart which is associated to a customer.
     * @param $target ShoppingCart
     * @param $products ShoppingCartProduct[]
     * @param $targetCartProducts ShoppingCartProduct[]
     * @return ShoppingCartProduct[] products of target shopping cart
     * @throws WrongQuantityException
     */
    public function toCustomerCartProducts(ShoppingCart $target,
                                           array $products,
                                           array $targetCartProducts = []) : array
    {
        foreach ($products as $product) {
            if (isset($targetCartProducts[$product->getProductId()])) {
                $targetCartProduct = $targetCartProducts[$product->getProductId()];
                $targetCartProduct->addQuantity((int)$product->getQuantity());
                continue;
            }
            $product->setShoppingCartId($target->getId());
            $this->discount->applyDiscount($target, $product);
            $targetCartProducts[] = $product;
        }
        return $targetCartProducts;
    }
}