<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\NObject;
use Nette\Utils\Random;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartHash extends NObject
{


    /**
     * @return string
     */
    public static function generateHash() : string
    {
        return Random::generate(32);
    }



    /**
     * @param ShoppingCart $shoppingCart
     * @param $hash string
     * @return ShoppingCart
     */
    public function setHash(ShoppingCart $shoppingCart, string $hash) : ShoppingCart
    {
        $shoppingCart->setHash($hash);
        return $shoppingCart;
    }
}