<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use Nette\Utils\Random;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductHash
{


    /**
     * @return string
     */
    public static function generateHash() : string
    {
        return sprintf('%s%s', Random::generate(32, '0-9a-z'), time());
    }



    /**
     * @param ShoppingCartProduct $shoppingCartProduct
     * @param string $hash
     * @return ShoppingCartProduct
     */
    public function setHash(ShoppingCartProduct $shoppingCartProduct, string $hash) : ShoppingCartProduct
    {
        $shoppingCartProduct->setHash($hash);
        return $shoppingCartProduct;
    }
}