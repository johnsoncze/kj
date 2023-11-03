<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Calculator;

use App\Customer\Customer;
use App\Product\Diamond\DiamondCollection;
use App\Product\Product;
use App\Product\Price\Base\Calculator AS ProductPriceBaseCalculator;
use App\Product\Production\Calculator\Calculator AS ProductionCalculator;
use App\Product\Production\ProductionTimeDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Calculator
{


    /** @var ProductionCalculator */
    protected $productionCalculator;

    /** @var ProductPriceBaseCalculator */
    protected $productPriceBaseCalculator;



    public function __construct(ProductionCalculator $calculator,
                                ProductPriceBaseCalculator $productPriceBaseCalculator)
    {
        $this->productionCalculator = $calculator;
        $this->productPriceBaseCalculator = $productPriceBaseCalculator;
    }



    /**
     * @param $customer Customer|null
     * @param $product Product
     * @param $productionTime ProductionTimeDTO
     * @param $diamondCollection DiamondCollection|null
     * @param $diamondQualityId int|null
     * @return \App\Price\Price
     * @throws \InvalidArgumentException
     * todo test
     */
    public function calculate(Customer $customer = NULL,
                              Product $product,
                              ProductionTimeDTO $productionTime,
                              DiamondCollection $diamondCollection = NULL,
                              int $diamondQualityId = NULL) : \App\Price\Price
    {
        $basePrice = $this->productPriceBaseCalculator->calculate($product, $diamondCollection);
        return $this->productionCalculator->calculate($customer, $basePrice, $productionTime, $diamondCollection, $diamondQualityId);
    }
}