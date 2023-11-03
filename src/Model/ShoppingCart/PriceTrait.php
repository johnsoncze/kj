<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait PriceTrait
{


    /**
     * @param $discount string|float|null
     * @throws \InvalidArgumentException
     */
    public function setDiscount($discount)
    {
        Entities::hasProperty($this, 'discount');
        if((float)$discount > 100){
            throw new \InvalidArgumentException(sprintf('Discount can be max 100 %%. %f given.', $discount));
        }
        $this->discount = $discount;
    }



    /**
     * @return string|float|null
     */
    public function getDiscount()
    {
        Entities::hasProperty($this, 'discount');
        return $this->discount;
    }



    /**
     * @param $price string|float|null
     */
    public function setPrice($price)
    {
        Entities::hasProperty($this, 'price');
        $this->price = $price;
    }



    /**
     * @return string|float|null
     */
    public function getPrice()
    {
        Entities::hasProperty($this, 'price');
        return $this->price;
    }



    /**
     * @param $vat string|float|null
     */
    public function setVat($vat)
    {
        Entities::hasProperty($this, 'vat');
        $this->vat = $vat;
    }



    /**
     * @return string|float|null
     */
    public function getVat()
    {
        Entities::hasProperty($this, 'vat');
        return $this->vat;
    }
}