<?php

declare(strict_types = 1);

namespace App\Price;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IPrice
{


    /**
     * @return float
     */
    public function getSummaryPrice();



    /**
     * @return float
     */
    public function getSummaryPriceWithoutVat();



    /**
     * @return float
     */
    public function getSummaryPriceBeforeDiscount();



    /**
     * @return float
     */
    public function getSummaryPriceBeforeDiscountWithoutVat();
}