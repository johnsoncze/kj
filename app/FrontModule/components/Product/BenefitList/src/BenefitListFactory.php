<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\BenefitList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface BenefitListFactory
{


    /**
     * @return BenefitList
     */
    public function create();
}