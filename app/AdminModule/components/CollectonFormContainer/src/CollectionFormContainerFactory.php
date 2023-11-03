<?php

namespace App\Components\CollectionFormContainer;

interface CollectionFormContainerFactory
{


    /**
     * @return CollectionFormContainer
     */
    public function create();
}