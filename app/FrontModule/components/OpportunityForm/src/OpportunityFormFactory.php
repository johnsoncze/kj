<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\OpportunityForm;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpportunityFormFactory
{


    /**
     * @return OpportunityForm
     */
    public function create();
}