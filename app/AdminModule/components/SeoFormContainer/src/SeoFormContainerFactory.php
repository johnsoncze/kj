<?php

namespace App\Components\SeoFormContainer;

interface SeoFormContainerFactory
{


    /**
     * @return SeoFormContainer
     */
    public function create();
}