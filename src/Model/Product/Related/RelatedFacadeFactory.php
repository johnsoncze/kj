<?php

declare(strict_types = 1);

namespace App\Product\Related;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface RelatedFacadeFactory
{


    /**
     * @return RelatedFacade
     */
    public function create();
}