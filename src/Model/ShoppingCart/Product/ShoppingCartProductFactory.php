<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use App\Product\Product;
use App\ShoppingCart\IShoppingCartPrice;
use App\ShoppingCart\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductFactory
{


    /**
     * @param Product $product
     * @param ShoppingCart $shoppingCart
     * @param $quantity int
     * @return ShoppingCartProduct
     */
    public function createFromProduct(Product $product,
                                      ShoppingCart $shoppingCart,
                                      int $quantity) : ShoppingCartProduct
    {
        $shoppingCartProduct = new ShoppingCartProduct();
        $shoppingCartProduct->setCatalogProduct($product);
        $shoppingCartProduct->setName($product->getTranslation()->getName());
        $shoppingCartProduct->setShoppingCartId($shoppingCart->getId());
        $shoppingCartProduct->setProductId($product->getId());
        $shoppingCartProduct->setQuantity($quantity);
        $shoppingCartProduct->setPrice($product->getPrice());
        $shoppingCartProduct->setVat($product->getVat());
        $shoppingCartProduct->setDiscount(IShoppingCartPrice::DEFAULT_DISCOUNT);
        $shoppingCartProduct->setAddDate(new \DateTime());

        return $shoppingCartProduct;
    }
}