<?php

declare(strict_types = 1);

namespace App\Tests\Opportunity\Product;

use App\Opportunity\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductTestTrait
{


    /**
     * @return Product
     */
    public function createTestOpportunityProduct() : Product
    {
        $product = new Product();
        $product->setOpportunityId(1);
        $product->setProductId(1);
        $product->setExternalSystemId(1);
        $product->setName('Produkt 1');
        $product->setCode('PR12345');
        $product->setUrl('produkt-1');
        $product->setPrice(250.50);
        $product->setVat(21.0);
        $product->setStock(FALSE);

        return $product;
    }
}