<?php

declare(strict_types = 1);

namespace App\Order\Price;

use App\BaseEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractPriceEntity extends BaseEntity
{


    /** @var float|null */
    protected $summaryPrice;

    /** @var float|null */
    protected $summaryPriceWithoutVat;

    /** @var float|null */
    protected $summaryPriceBeforeDiscount;

    /** @var float|null */
    protected $summaryPriceBeforeDiscountWithoutVat;



    /**
     * @param $price float
     * @return self
     */
    public function setSummaryPrice($price) : self
    {
        $this->summaryPrice = $price;
        return $this;
    }



    /**
     * @return float|null
     */
    public function getSummaryPrice()
    {
        return $this->summaryPrice;
    }



    /**
     * @return float|null
     */
    public function getSummaryPriceWithoutVat()
    {
        return $this->summaryPriceWithoutVat;
    }



    /**
     * @param float|null $summaryPriceWithoutVat
     * @return self
     */
    public function setSummaryPriceWithoutVat($summaryPriceWithoutVat)
    {
        $this->summaryPriceWithoutVat = $summaryPriceWithoutVat;
        return $this;
    }



    /**
     * @return float|null
     */
    public function getSummaryPriceBeforeDiscount()
    {
        return $this->summaryPriceBeforeDiscount;
    }



    /**
     * @param float|null $summaryPriceBeforeDiscount
     * @return self
     */
    public function setSummaryPriceBeforeDiscount($summaryPriceBeforeDiscount)
    {
        $this->summaryPriceBeforeDiscount = $summaryPriceBeforeDiscount;
        return $this;
    }



    /**
     * @return float|null
     */
    public function getSummaryPriceBeforeDiscountWithoutVat()
    {
        return $this->summaryPriceBeforeDiscountWithoutVat;
    }



    /**
     * @param float|null $summaryPriceBeforeDiscountWithoutVat
     * @return self
     */
    public function setSummaryPriceBeforeDiscountWithoutVat($summaryPriceBeforeDiscountWithoutVat)
    {
        $this->summaryPriceBeforeDiscountWithoutVat = $summaryPriceBeforeDiscountWithoutVat;
        return $this;
    }


}