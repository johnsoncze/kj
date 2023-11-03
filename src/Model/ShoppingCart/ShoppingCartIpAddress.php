<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartIpAddress extends NObject
{


    /** @var string */
    const CONSOLE = 'console';



    /**
     * @param ShoppingCart $shoppingCart
     * @return ShoppingCart
     */
    public function setIpAddress(ShoppingCart $shoppingCart) : ShoppingCart
    {
        $shoppingCart->setIpAddress($_SERVER['SERVER_ADDR'] ?? self::CONSOLE);
        return $shoppingCart;
    }
}