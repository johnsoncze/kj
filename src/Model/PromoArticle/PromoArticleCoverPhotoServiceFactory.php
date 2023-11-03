<?php

namespace App\PromoArticle;


interface PromoArticleCoverPhotoServiceFactory
{


    /**
     * @return PromoArticleCoverPhotoService
     */
    public function create();
}