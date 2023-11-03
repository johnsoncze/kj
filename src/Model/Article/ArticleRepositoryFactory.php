<?php

namespace App\Article;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleRepositoryFactory
{


    /**
     * @return \App\Article\ArticleRepository
     */
    public function create();
}