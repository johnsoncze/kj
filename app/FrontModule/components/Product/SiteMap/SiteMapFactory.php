<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\SiteMap;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SiteMapFactory
{


    /**
     * @return SiteMap
     */
    public function create();
}