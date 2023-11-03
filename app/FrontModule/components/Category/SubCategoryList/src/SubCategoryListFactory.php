<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\SubCategoryList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SubCategoryListFactory
{


    /**
     * @return SubCategoryList
     */
    public function create();
}