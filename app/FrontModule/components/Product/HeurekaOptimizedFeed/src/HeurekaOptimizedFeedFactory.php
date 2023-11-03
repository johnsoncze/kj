<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\HeurekaOptimizedFeed;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface HeurekaOptimizedFeedFactory
{
    /**
     * @return HeurekaOptimizedFeed
     */
    public function create();
}