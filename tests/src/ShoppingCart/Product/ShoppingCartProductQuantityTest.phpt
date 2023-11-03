<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductQuantity;
use App\ShoppingCart\Product\WrongQuantityException;
use App\ShoppingCart\ShoppingCartHash;
use App\ShoppingCart\ShoppingCartTranslation;
use App\Tests\BaseTestCase;
use Kdyby\Translation\ITranslator;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductQuantityTest extends BaseTestCase
{


    public function testSetQuantity()
    {
        $hash = ShoppingCartHash::generateHash();
        $product = new ShoppingCartProduct();
        $product->setShoppingCartId(5);
        $product->setProductId(77);
        $product->setPrice(450.50);
        $product->setVat(21.00);
        $product->setQuantity(55);
        $product->setDiscount(15.00);
        $product->setHash($hash);

        $newQuantity = 150;
        $quantity = new ShoppingCartProductQuantity();
        $quantity->setQuantity($product, $newQuantity, $this->container->getByType(ITranslator::class));

        Assert::same($newQuantity, $product->getQuantity());
        Assert::same(5, $product->getShoppingCartId());
        Assert::same(77, $product->getProductId());
        Assert::same(450.50, $product->getPrice());
        Assert::same(21.00, $product->getVat());
        Assert::same(15.00, $product->getDiscount());
        Assert::same($hash, $product->getHash());
    }



    public function testAddQuantity()
    {
        $hash = ShoppingCartHash::generateHash();
        $actualQuantity = 55;
        $product = new ShoppingCartProduct();
        $product->setShoppingCartId(5);
        $product->setProductId(77);
        $product->setPrice(450.50);
        $product->setVat(21.00);
        $product->setQuantity($actualQuantity);
        $product->setDiscount(15.00);
        $product->setHash($hash);

        $addQuantity = 150;
        $quantity = new ShoppingCartProductQuantity();
        $quantity->addQuantity($product, $addQuantity, $this->container->getByType(ITranslator::class));

        Assert::same($actualQuantity + $addQuantity, (int)$product->getQuantity());
        Assert::same(5, $product->getShoppingCartId());
        Assert::same(77, $product->getProductId());
        Assert::same(450.50, $product->getPrice());
        Assert::same(21.00, $product->getVat());
        Assert::same(15.00, $product->getDiscount());
        Assert::same($hash, $product->getHash());
    }



    public function testSetWrongQuantity()
    {
        $hash = ShoppingCartHash::generateHash();
        $product = new ShoppingCartProduct();
        $product->setShoppingCartId(5);
        $product->setProductId(77);
        $product->setPrice(450.50);
        $product->setVat(21.00);
        $product->setQuantity(55);
        $product->setDiscount(15.00);
        $product->setHash($hash);

        $quantity = new ShoppingCartProductQuantity();

        Assert::exception(function () use ($product, $quantity) {
            $quantity->setQuantity($product, 0, $this->container->getByType(ITranslator::class));
        }, WrongQuantityException::class, sprintf('%s.product.wrong.quantity', ShoppingCartTranslation::getFileName()));

        Assert::exception(function () use ($product, $quantity) {
            $quantity->setQuantity($product, -1, $this->container->getByType(ITranslator::class));
        }, WrongQuantityException::class, sprintf('%s.product.wrong.quantity', ShoppingCartTranslation::getFileName()));

        Assert::same(5, $product->getShoppingCartId());
        Assert::same(77, $product->getProductId());
        Assert::same(450.50, $product->getPrice());
        Assert::same(21.00, $product->getVat());
        Assert::same(15.00, $product->getDiscount());
        Assert::same($hash, $product->getHash());
    }
}

(new ShoppingCartProductQuantityTest())->run();