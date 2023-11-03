<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\SimilarCategoryList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SimilarCategoryListFactory
{


    /**
     * @return SimilarCategoryList
     */
    public function create();
}