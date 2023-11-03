<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\WeedingRing\Demand;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DemandFactory
{


    /**
     * @return Demand
     */
    public function create();
}