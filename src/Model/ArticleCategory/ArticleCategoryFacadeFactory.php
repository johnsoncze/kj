<?php

namespace App\ArticleCategory;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleCategoryFacadeFactory
{


    /**
     * @return ArticleCategoryFacade
     */
    public function create();
}