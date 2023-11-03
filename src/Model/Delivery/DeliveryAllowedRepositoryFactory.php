<?php

declare(strict_types = 1);

namespace App\Delivery;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DeliveryAllowedRepositoryFactory
{


    /**
     * @return DeliveryAllowedRepository
     */
    public function create();
}