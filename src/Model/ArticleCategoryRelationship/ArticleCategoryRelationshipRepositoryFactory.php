<?php

namespace App\ArticleCategoryRelationship;

interface ArticleCategoryRelationshipRepositoryFactory
{


    /**
     * @return ArticleCategoryRelationshipRepository
     */
    public function create();
}