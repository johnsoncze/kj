<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product\Price;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PriceDTO
{


    public $vat = 0.0;
    public $discount = 0.0;

    public $unitPrice = 0.0;
    public $unitPriceWithoutVat = 0.0;
    public $unitPriceBeforeDiscount = 0.0;
    public $unitPriceBeforeDiscountWithoutVat = 0.0;

    public $surchargePercent = 0.0;
    public $surcharge = 0.0;
    public $surchargeWithoutVat = 0.0;

    public $summaryPrice = 0.0;
    public $summaryPriceWithoutVat = 0.0;
    public $summaryPriceBeforeDiscount = 0.0;
    public $summaryPriceBeforeDiscountWithoutVat = 0.0;
}