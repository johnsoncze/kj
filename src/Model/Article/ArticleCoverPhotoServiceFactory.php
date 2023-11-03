<?php

namespace App\Article;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleCoverPhotoServiceFactory
{


    /**
     * @return ArticleCoverPhotoService
     */
    public function create();
}