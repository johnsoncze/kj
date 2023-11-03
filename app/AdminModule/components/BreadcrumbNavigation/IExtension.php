<?php

namespace App\Components\BreadcrumbNavigation;

interface IExtension
{


    /**
     * Load extension
     * @param $navigation Navigation
     * @return void
     */
    public function load(Navigation $navigation);
}