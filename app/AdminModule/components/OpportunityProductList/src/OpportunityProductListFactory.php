<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OpportunityProductList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpportunityProductListFactory
{


    /**
     * @return OpportunityProductList
     */
    public function create();
}