<?php

namespace App\Components\PageTextForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageTextFormFactory
{


    /**
     * @return PageTextForm
     */
    public function create();
}