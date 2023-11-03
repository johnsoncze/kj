<?php

declare(strict_types = 1);

namespace App\Opportunity;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpportunityStorageFacadeFactory
{


    /**
     * @return OpportunityStorageFacade
     */
    public function create();
}