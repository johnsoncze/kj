<?php

declare(strict_types = 1);

namespace App\Tests\Product;

use App\Product\Product;
use App\Product\ProductFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductFactoryTest extends BaseTestCase
{


    public function testCreate()
    {
        $code = 'znh77-778';
        $photo = NULL;
        $stockState = 3;
        $emptyStockState = 5;
        $stock = 77;
        $price = 7887.45;
        $vat = 21.000;
        $state = Product::PUBLISH;
        $new = FALSE;
        $saleOnline = TRUE;

        $productFactory = new ProductFactory();
        $product = $productFactory->create($code, $photo, $stockState, $emptyStockState, $stock, $price, $vat, $state, $new, $saleOnline);

        Assert::same($code, $product->getCode());
        Assert::same($photo, $product->getPhoto());
        Assert::same($stockState, $product->getStockState());
        Assert::same($emptyStockState, $product->getEmptyStockState());
        Assert::same($stock, $product->getStock());
        Assert::same($price, $product->getPrice());
        Assert::same($vat, (float)$product->getVat());
        Assert::same($state, $product->getState());
        Assert::same($new, $product->getNew());
        Assert::same($saleOnline, $product->getSaleOnline());
    }
}

(new ProductFactoryTest())->run();