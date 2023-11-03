<?php

namespace App\ArticleCategoryRelationship;

use App\ArticleCategory\ArticleCategoryRelationshipSetService;


interface ArticleCategoryRelationshipSetServiceFactory
{


    /**
     * @return ArticleCategoryRelationshipSetService
     */
    public function create();
}