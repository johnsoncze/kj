<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryParameterList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryParameterListFactory
{


    /**
     * @return CategoryParameterList
     */
    public function create();
}