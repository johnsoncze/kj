<?php

namespace App\Components\CategoryForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFormProcessSuccessFactory
{


    /**
     * @return CategoryFormProcessSuccess
     */
    public function create();
}