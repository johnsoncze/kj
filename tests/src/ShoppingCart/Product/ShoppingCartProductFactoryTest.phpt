<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\ShoppingCart\Product\ShoppingCartProductFactory;
use App\ShoppingCart\ShoppingCart;
use App\Tests\BaseTestCase;
use App\Tests\Product\ProductTestTrait;
use App\Tests\Product\Translation\ProductTranslationTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductFactoryTest extends BaseTestCase
{


    use ProductTestTrait;
    use ProductTranslationTestTrait;



    public function testCreate()
    {
        $product = $this->createTestProduct();
        $productTranslation = $this->createTestProductTranslation();
        $product->addTranslation($productTranslation);

        $shoppingCart = new ShoppingCart();
        $shoppingCart->setId(95);

        $quantity = 10;

        $factory = new ShoppingCartProductFactory();
        $cartProduct = $factory->createFromProduct($product, $shoppingCart, $quantity);
        $cartProduct->setQuantity($quantity);

        Assert::same($shoppingCart->getId(), $cartProduct->getShoppingCartId());
        Assert::same($product->getId(), $cartProduct->getProductId());
        Assert::same($quantity, $cartProduct->getQuantity());
        Assert::same($product->getPrice(), $cartProduct->getPrice());
        Assert::same($product->getVat(), $cartProduct->getVat());
    }
}

(new ShoppingCartProductFactoryTest())->run();