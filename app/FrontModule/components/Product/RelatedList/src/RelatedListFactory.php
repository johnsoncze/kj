<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\RelatedList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface RelatedListFactory
{


    /**
     * @return RelatedList
     */
    public function create();
}