<?php

namespace App\Article;

use App\ArticleCategory\ArticleCategoryEntity;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @method getArticleEntity()
 * @method getArticleCategories()
 */
class ArticleAggregate extends NObject
{


    /** @var ArticleEntity */
    protected $articleEntity;

    /** @var ArticleCategoryEntity[] */
    protected $articleCategories;



    public function __construct(ArticleEntity $articleEntity)
    {
        $this->articleEntity = $articleEntity;
    }



    /**
     * @param $articleCategoryEntity ArticleCategoryEntity
     * @return void
     */
    public function addCategory(ArticleCategoryEntity $articleCategoryEntity)
    {
        $this->articleCategories[] = $articleCategoryEntity;
    }
}