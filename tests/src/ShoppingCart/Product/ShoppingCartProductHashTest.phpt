<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductHash;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductHashTest extends BaseTestCase
{


    public function testSetHash()
    {
        $shoppingCartId = 5;
        $productId = 77;
        $price = 450.50;
        $vat = 21.00;
        $quantity = 55;
        $discount = 15.00;

        $cartProduct = new ShoppingCartProduct();
        $cartProduct->setShoppingCartId($shoppingCartId);
        $cartProduct->setProductId($productId);
        $cartProduct->setPrice($price);
        $cartProduct->setVat($vat);
        $cartProduct->setQuantity($quantity);
        $cartProduct->setDiscount($discount);

        $hash = ShoppingCartProductHash::generateHash();
        $hashService = new ShoppingCartProductHash();
        $hashService->setHash($cartProduct, $hash);

        Assert::same($shoppingCartId, $cartProduct->getShoppingCartId());
        Assert::same($productId, $cartProduct->getProductId());
        Assert::same($price, $cartProduct->getPrice());
        Assert::same($vat, $cartProduct->getVat());
        Assert::same($quantity, $cartProduct->getQuantity());
        Assert::same($discount, $cartProduct->getDiscount());
        Assert::same($hash, $cartProduct->getHash());
    }
}

(new ShoppingCartProductHashTest())->run();