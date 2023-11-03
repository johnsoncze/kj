<?php

declare(strict_types = 1);

namespace App\Price;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PriceCalculator
{


    /**
     * @param $prices IPrice[]
     * @return Price
     * @throws \InvalidArgumentException
     */
    public function summary(array $prices) : Price
    {
        $priceDTO = new Price();
        foreach ($prices as $price) {
            if ($price instanceof IPrice) {
                throw new \InvalidArgumentException(sprintf('Object must be instance of \'%s\'.', $price));
            }
            $priceDTO->summary += $price->getSummaryPrice();
            $priceDTO->summaryWithoutVat += $price->getSummaryPriceWithoutVat();
            $priceDTO->summaryBeforeDiscount += $price->getSummaryPriceBeforeDiscount();
            $priceDTO->summaryBeforeDiscountWithoutVat += $price->getSummaryPriceBeforeDiscountWithoutVat();
        }
        return $priceDTO;
    }
}