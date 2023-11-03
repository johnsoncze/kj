<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\DeliveryList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DeliveryListFactory
{


    /**
     * @return DeliveryList
     */
    public function create();
}