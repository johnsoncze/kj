<?php

namespace App\Components\UrlFormContainer;

interface UrlFormContainerFactory
{


    /**
     * @return UrlFormContainer
     */
    public function create();
}