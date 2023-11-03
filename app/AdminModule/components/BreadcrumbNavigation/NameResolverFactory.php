<?php

namespace App\Components\BreadcrumbNavigation;

interface NameResolverFactory
{


    /**
     * @return NameResolver
     */
    public function create();
}