<?php

declare(strict_types = 1);

namespace App\Product\Price\Base;

use App\Diamond\Diamond;
use App\Product\Diamond\DiamondCollection;
use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * Object calculating product base price without diamonds and another components.
 */
class Calculator
{


    /**
     * @param $product Product
     * @param $diamondCollection DiamondCollection
     * @return Price
     * @throws \InvalidArgumentException
     * todo test
     */
    public function calculate(Product $product, DiamondCollection $diamondCollection = NULL) : Price
    {
        $price = $product->getPrice();
        if ($diamondCollection !== NULL) {
            $productDiamonds = $diamondCollection->getDiamonds();
            foreach ($productDiamonds as $productDiamond) {
                /** @var $diamond Diamond */
                $diamond = $productDiamond->getDiamond();
                $diamondPrice = $diamond->getPriceByQualityId($diamond->getDefaultQualityId());
                $price -= $diamondPrice->getPrice() * $productDiamond->getQuantity();
            }
        }
        return new Price((float)$price, (float)$product->getVat());
    }
}