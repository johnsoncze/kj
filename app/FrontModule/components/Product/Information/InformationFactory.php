<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Information;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface InformationFactory
{


    /**
     * @return Information
     */
    public function create();
}