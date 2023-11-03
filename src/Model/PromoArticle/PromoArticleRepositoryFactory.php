<?php

namespace App\PromoArticle;



interface PromoArticleRepositoryFactory
{


    /**
     * @return \App\PromoArticle\PromoArticleRepository
     */
    public function create();
}