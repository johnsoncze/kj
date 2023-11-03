<?php

namespace App\Article;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleFacadeFactory
{


    /**
     * @return ArticleFacade
     */
    public function create();
}