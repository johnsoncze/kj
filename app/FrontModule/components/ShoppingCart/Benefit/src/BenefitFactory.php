<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\Benefit;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface BenefitFactory
{


    /**
     * @return Benefit
     */
    public function create();
}