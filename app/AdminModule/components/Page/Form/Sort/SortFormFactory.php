<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Page\Form\Sort;

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