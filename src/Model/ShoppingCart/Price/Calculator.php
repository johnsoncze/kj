<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Price;

use App\ShoppingCart\Product\Price\PriceDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Calculator
{


    /**
     * @param $productPrices PriceDTO[]
     * @return Price
     */
    public function calculate(array $productPrices) : Price
    {
        $price = new Price();
        foreach ($productPrices as $productPrice) {
            $price->summaryPrice += $productPrice->summaryPrice;
            $price->summaryPriceWithoutVat += $productPrice->summaryPriceWithoutVat;
            $price->summaryPriceBeforeDiscount += $productPrice->summaryPriceBeforeDiscount;
            $price->summaryPriceBeforeDiscountWithoutVat += $productPrice->summaryPriceBeforeDiscountWithoutVat;
            $price->productSummaryPriceWithoutVat += $productPrice->summaryPriceWithoutVat;
        }
        return $price;
    }
}