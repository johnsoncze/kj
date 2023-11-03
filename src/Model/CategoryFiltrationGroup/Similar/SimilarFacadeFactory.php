<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup\Similar;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SimilarFacadeFactory
{


    /**
     * @return SimilarFacade
     */
    public function create();
}