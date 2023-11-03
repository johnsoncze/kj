<?php

declare(strict_types = 1);

namespace App\Product\Price\Base;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Price
{


    /** @var float */
    protected $price;

    /** @var float */
    protected $vat;



    public function __construct(float $price, float $vat)
    {
        $this->price = $price;
        $this->vat = $vat;
    }



    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }



    /**
     * @return float
     */
    public function getVat(): float
    {
        return $this->vat;
    }


}