<?php

namespace App\ArticleCategory;

interface ArticleCategoryRepositoryFactory
{


    /**
     * @return ArticleCategoryRepository
     */
    public function create();
}