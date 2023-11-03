<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Article\ArticleList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleListFactory
{


    /**
     * @return ArticleList
     */
    public function create();
}