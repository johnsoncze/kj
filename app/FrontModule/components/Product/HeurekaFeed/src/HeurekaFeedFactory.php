<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\HeurekaFeed;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface HeurekaFeedFactory
{


    /**
     * @return HeurekaFeed
     */
    public function create();
}