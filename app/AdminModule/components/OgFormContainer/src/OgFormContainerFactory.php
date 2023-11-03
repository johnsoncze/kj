<?php

namespace App\Components\OgFormContainer;

interface OgFormContainerFactory
{


    /**
     * @return OgFormContainer
     */
    public function create();
}