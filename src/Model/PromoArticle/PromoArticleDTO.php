<?php

declare(strict_types = 1);

namespace App\PromoArticle;


class PromoArticleDTO extends ArticleAggregate
{


    /**
     * @return PromoArticleEntity
     */
    public function getPromoArticle(): PromoArticleEntity
    {
        return $this->promoArticleEntity;
    }
}