<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Category\Product\Sorting\Sorting;
use App\Helpers\Prices;
use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class FromTheCheapest extends BaseSorter
{


    /**
     * @inheritdoc
     */
    protected function resolveSorting(Product $product) : Sorting
    {
        $price = Prices::toString($product->getPrice());
        return $this->createSortingObject($product, $price);
    }

}