<?php

namespace App\ArticleCategoryRelationship;

interface ArticleCategoryRelationshipFacadeFactory
{


    /**
     * @return ArticleCategoryRelationshipFacade
     */
    public function create();
}