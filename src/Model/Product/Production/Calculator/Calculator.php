<?php

declare(strict_types = 1);

namespace App\Product\Production\Calculator;

use App\Customer\Customer;
use App\Diamond\Diamond;
use App\Helpers\Prices;
use App\Product\Diamond\DiamondCollection;
use App\Product\Production\ProductionTimeDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Calculator
{


    /**
     * @param $customer Customer
     * @param $basePrice \App\Product\Price\Base\Price
     * @param $productionTime ProductionTimeDTO
     * @param $diamondCollection DiamondCollection
     * @param $diamondQualityId int|null
     * @return \App\Price\Price
     * @throws \InvalidArgumentException
     * todo test
	 * todo replace ProductionTimeDTO by Time object
     */
    public function calculate(Customer $customer = NULL,
                              \App\Product\Price\Base\Price $basePrice,
                              ProductionTimeDTO $productionTime,
                              DiamondCollection $diamondCollection = NULL,
                              int $diamondQualityId = NULL) : \App\Price\Price
    {
        $diamondVat = [];
        $summaryPriceBeforeDiscount = $basePrice->getPrice();
        $productDiamonds = $diamondCollection ? $diamondCollection->getDiamonds() : [];
        foreach ($productDiamonds as $productDiamond) {
            /** @var $diamond Diamond */
            $diamond = $productDiamond->getDiamond();
            $diamondPrice = $diamond->getPriceByQualityId($diamondQualityId);
            $summaryPriceBeforeDiscount += $diamondPrice->getPrice() * $productDiamond->getQuantity();
            $diamondVat[] = $diamondPrice->getVat();
        }
        $summaryPriceBeforeDiscount = $productionTime->hasSurcharge() === TRUE ? Prices::addPercent($summaryPriceBeforeDiscount, $productionTime->getSurchargePercent()) : $summaryPriceBeforeDiscount;

        $vatPercent = $basePrice->getVat();
        $vatPercent += $diamondVat ? array_sum($diamondVat) : 0;
        $vatPercent /= 1 + count($diamondVat); //vat from price + vat from diamonds

        $price = new \App\Price\Price();
        $price->summary = $customer !== NULL ? Prices::subtractPercent($summaryPriceBeforeDiscount, Customer::DISCOUNT) : $summaryPriceBeforeDiscount;
        $price->summaryWithoutVat = Prices::toBeforeDiscount($price->summary, $vatPercent);
        $price->summaryBeforeDiscount = $summaryPriceBeforeDiscount;
        $price->summaryBeforeDiscountWithoutVat = Prices::toBeforePercent($price->summaryBeforeDiscount, $vatPercent);

        return $price;
    }
}