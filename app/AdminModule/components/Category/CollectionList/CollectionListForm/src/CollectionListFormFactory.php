<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\CollectionListForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CollectionListFormFactory
{


    /**
     * @return CollectionListForm
     */
    public function create();
}