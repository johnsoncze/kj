<?php

namespace App\ArticleCategory;

interface ArticleCategoryDuplicateServiceFactory
{


    /**
     * @return ArticleCategoryDuplicateService
     */
    public function create();
}