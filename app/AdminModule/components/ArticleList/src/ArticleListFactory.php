<?php

namespace App\Components\ArticleList;

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