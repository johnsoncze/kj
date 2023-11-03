<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\FacebookOptimizedFeed;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface FacebookOptimizedFeedFactory
{


    /**
     * @return FacebookOptimizedFeed
     */
    public function create();
}