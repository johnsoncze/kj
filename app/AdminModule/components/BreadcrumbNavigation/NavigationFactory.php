<?php

namespace App\Components\BreadcrumbNavigation;

interface NavigationFactory
{


    /**
     * @return Navigation
     */
    public function create();
}