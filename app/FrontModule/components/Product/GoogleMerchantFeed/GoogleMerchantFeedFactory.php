<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\GoogleMerchantFeed;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface GoogleMerchantFeedFactory
{


    /**
     * @return GoogleMerchantFeed
     */
    public function create();
}