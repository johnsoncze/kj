<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface FiltrationFactory
{


    /**
     * @return Filtration
     */
    public function create();
}