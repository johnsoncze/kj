<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\CollectionList;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CollectionListFactory
{


    /**
     * @return CollectionList
     */
    public function create();
}