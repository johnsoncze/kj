<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\TopCategory;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface TopCategoryFactory
{


    /**
     * @return TopCategory
     */
    public function create();
}