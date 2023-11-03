<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OpportunityList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpportunityListFactory
{


    /**
     * @return OpportunityList
     */
    public function create();
}