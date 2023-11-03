<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductQuantity
{


    /**
     * @param ShoppingCartProduct $product
     * @param int $quantity
     * @param ITranslator $translator
     * @return ShoppingCartProduct
     * todo může dělat entita sama
     */
    public function setQuantity(ShoppingCartProduct $product, int $quantity, ITranslator $translator) : ShoppingCartProduct
    {
        $product->setQuantity($quantity, $translator);
        return $product;
    }



    /**
     * @param ShoppingCartProduct $product
     * @param int $quantity
     * @param ITranslator $translator
     * @return ShoppingCartProduct
     * todo toto může dělat entita sama
     */
    public function addQuantity(ShoppingCartProduct $product, int $quantity, ITranslator $translator) : ShoppingCartProduct
    {
        $product->setQuantity((int)$product->getQuantity() + $quantity, $translator);
        return $product;
    }
}