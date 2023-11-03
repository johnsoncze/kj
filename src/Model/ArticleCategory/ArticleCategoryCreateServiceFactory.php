<?php

namespace App\ArticleCategory;

interface ArticleCategoryCreateServiceFactory
{


    /**
     * @return ArticleCategoryCreateService
     */
    public function create();
}