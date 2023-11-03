<?php

namespace App\Components\ArticleForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleFormFactory
{


    /**
     * @return ArticleForm
     */
    public function create();
}