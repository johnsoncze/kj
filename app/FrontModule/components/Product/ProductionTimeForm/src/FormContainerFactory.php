<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ProductionTimeForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface FormContainerFactory
{


    /**
     * @return FormContainer
     */
    public function create();
}