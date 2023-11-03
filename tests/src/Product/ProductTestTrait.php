<?php

declare(strict_types = 1);

namespace App\Tests\Product;

use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductTestTrait
{


    /**
     * @return Product
     */
    public function createTestProduct() : Product
    {
        $product = new Product();
        $product->setCode('ZN12345');
        $product->setExternalSystemId(1);
        $product->setPhoto('photo-123.png');
        $product->setStockState(1);
        $product->setEmptyStockState(2);
        $product->setStock(5);
        $product->setPrice(55.55);
        $product->setVat(21.0);
        $product->setState(Product::PUBLISH);
        $product->setCompleted(TRUE);
        $product->setNew(FALSE);
        $product->setSaleOnline(FALSE);

        return $product;
    }
}