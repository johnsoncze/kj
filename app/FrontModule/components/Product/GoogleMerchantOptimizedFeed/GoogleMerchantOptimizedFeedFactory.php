<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\GoogleMerchantOptimizedFeed;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface GoogleMerchantOptimizedFeedFactory
{


    /**
     * @return GoogleMerchantOptimizedFeed
     */
    public function create();
}