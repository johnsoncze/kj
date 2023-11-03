<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IShoppingCartPrice
{


    const DEFAULT_DISCOUNT = 0.0;



    /**
     * @param $discount string|float|null
     */
    public function setDiscount($discount);



    /**
     * @return string|float|null
     */
    public function getDiscount();



    /**
     * @param $price string|float|null
     */
    public function setPrice($price);



    /**
     * @return string|float|null
     */
    public function getPrice();



    /**
     * @param $vat string|float|null
     */
    public function setVat($vat);



    /**
     * @return string|float|null
     */
    public function getVat();
}