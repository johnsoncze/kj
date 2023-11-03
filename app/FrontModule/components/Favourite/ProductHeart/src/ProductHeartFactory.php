<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Favourite\ProductHeart;


interface ProductHeartFactory
{


    /**
     * @return ProductHeart
     */
    public function create();
}