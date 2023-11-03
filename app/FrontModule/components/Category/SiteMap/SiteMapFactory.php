<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\SiteMap;

interface SiteMapFactory
{


    /**
     * @return SiteMap
     */
    public function create();
}