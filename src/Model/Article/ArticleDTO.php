<?php

declare(strict_types = 1);

namespace App\Article;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleDTO extends ArticleAggregate
{


    /**
     * @return ArticleEntity
     */
    public function getArticle(): ArticleEntity
    {
        return $this->articleEntity;
    }



    /**
     * @return bool
     */
    public function hasCategory(): bool
    {
        return count($this->articleCategories) > 0;
    }
}