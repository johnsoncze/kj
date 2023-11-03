<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ZboziCzFeed;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ZboziCzFeedFactory
{


    /**
     * @return ZboziCzFeed
     */
    public function create();
}