<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryParameterForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryParameterFormFactory
{


    /**
     * @return CategoryParameterForm
     */
    public function create();
}