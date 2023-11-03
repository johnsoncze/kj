<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\OpportunityForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpportunityFormContainerFactory
{


    /**
     * @return OpportunityFormContainer
     */
    public function create();
}