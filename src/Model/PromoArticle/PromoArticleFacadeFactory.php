<?php

namespace App\PromoArticle;


interface PromoArticleFacadeFactory
{


    /**
     * @return PromoArticleFacade
     */
    public function create();
}