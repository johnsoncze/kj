<?php

namespace App\Components\RelatedPageContainer;

interface RelatedPageContainerFactory
{


    /**
     * @return RelatedPageContainer
     */
    public function create();
}