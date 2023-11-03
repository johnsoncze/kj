<?php

namespace App\Components\BreadcrumbNavigation;

interface INameResolver
{


    /**
     * Get name of route
     * @param $route string
     * @return string
     */
    public function getName($route);
}