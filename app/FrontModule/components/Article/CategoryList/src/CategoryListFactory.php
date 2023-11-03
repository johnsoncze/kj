<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Article\CategoryList;

use App\ArticleCategory\ArticleCategoryEntity;
use App\Page\PageEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryListFactory
{


    /**
     * @param $page PageEntity
     * @param $categories ArticleCategoryEntity[]|array
     * @return CategoryList
     */
    public function create(PageEntity $page, array $categories = []);
}