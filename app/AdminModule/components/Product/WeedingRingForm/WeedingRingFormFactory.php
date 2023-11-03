<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Product\WeedingRingForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface WeedingRingFormFactory
{


    /**
     * @return WeedingRingForm
     */
    public function create();
}