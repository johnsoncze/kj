<?php

declare(strict_types = 1);

namespace App\ProductState;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductStateRepositoryFactory
{


    /**
     * @return ProductStateRepository
     */
    public function create();
}