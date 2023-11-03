<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product\Price;

use App\ShoppingCart\Product\ShoppingCartProduct;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 * //todo zbytečný objekt
 */
class PriceCalculator
{


    /**
     * Calculate prices of product.
     * @param $product ShoppingCartProduct
     * @return PriceDTO
     * todo test
     */
    public function calculate(ShoppingCartProduct $product) : PriceDTO
    {
        $price = new PriceDTO();
        $price->vat = $product->getVat();
        $price->discount = $product->getDiscount();

        $price->unitPrice = $product->getUnitPrice();
        $price->unitPriceWithoutVat = $product->getUnitPriceWithoutVat();
        $price->unitPriceBeforeDiscount = $product->getUnitPriceBeforeDiscount();
        $price->unitPriceBeforeDiscountWithoutVat = $product->getUnitPriceBeforeDiscountWithoutVat();

        $price->surchargePercent = $product->getSurchargePercent();
        $price->surcharge = $product->getSurcharge();
        $price->surchargeWithoutVat = $product->getSurchargeWithoutVat();

        $price->summaryPrice = $product->getSummaryPrice();
        $price->summaryPriceWithoutVat = $product->getSummaryPriceWithoutVat();
        $price->summaryPriceBeforeDiscount = $product->getSummaryPriceBeforeDiscount();
        $price->summaryPriceBeforeDiscountWithoutVat = $product->getSummaryPriceBeforeDiscountWithoutVat();

        return $price;
    }
}