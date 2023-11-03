<?php

namespace App\Components\PromoArticleForm;


interface PromoArticleFormFactory
{


    /**
     * @return PromoArticleForm
     */
    public function create();
}