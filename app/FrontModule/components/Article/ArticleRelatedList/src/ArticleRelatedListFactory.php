<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Article\ArticleRelatedList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleRelatedListFactory
{


    /**
     * @return ArticleRelatedList
     */
    public function create();
}