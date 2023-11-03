<?php

namespace App\Components\ArticleCategoryList;

interface ArticleCategoryListFactory
{


    /**
     * @return ArticleCategoryList
     */
    public function create();
}