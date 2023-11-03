<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Calculator;

use App\Price\Price;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Calculation
{


    /** @var $male Price */
    protected $male;

    /** @var $female Price */
    protected $female;

    /** @var $summary Price|null */
    protected $summary;



    public function __construct(Price $male, Price $female)
    {
        $this->male = $male;
        $this->female = $female;
    }



    /**
     * @return Price
     */
    public function getMalePrice() : Price
    {
        return $this->male;
    }



    /**
     * @return Price
     */
    public function getFemalePrice() : Price
    {
        return $this->female;
    }



    /**
     * @return Price
     */
    public function getSummaryPrice() : Price
    {
        if ($this->summary === NULL) {
            $male = $this->getMalePrice();
            $female = $this->getFemalePrice();

            $this->summary = new Price();
            $this->summary->summary = $male->summary + $female->summary;
            $this->summary->summaryWithoutVat = $male->summaryWithoutVat + $female->summaryWithoutVat;
            $this->summary->summaryBeforeDiscount = $male->summaryBeforeDiscount + $female->summaryBeforeDiscount;
            $this->summary->summaryBeforeDiscountWithoutVat = $male->summaryBeforeDiscountWithoutVat + $female->summaryBeforeDiscountWithoutVat;
        }
        return $this->summary;
    }
}