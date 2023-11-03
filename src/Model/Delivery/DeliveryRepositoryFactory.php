<?php

declare(strict_types = 1);

namespace App\Delivery;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DeliveryRepositoryFactory
{


    /**
     * @return DeliveryRepository
     */
    public function create();
}