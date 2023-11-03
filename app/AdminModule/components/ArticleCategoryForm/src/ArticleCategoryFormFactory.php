<?php

namespace App\Components\ArticleCategoryForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleCategoryFormFactory
{


    /**
     * @return ArticleCategoryForm
     */
    public function create();
}