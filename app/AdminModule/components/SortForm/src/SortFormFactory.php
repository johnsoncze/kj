<?php

namespace App\Components\SortForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SortFormFactory
{


    /**
     * @return SortForm
     */
    public function create();
}